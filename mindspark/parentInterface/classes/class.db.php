<?php

require_once(dirname(__FILE__) . '/db_credentials.php');

class db extends PDO {

    private $error;
    private $sql;
    private $bind;
    private $errorCallbackFunction;
    private $errorMsgFormat;

    public function __construct($serverID = 'MASTER', $defaultDatabase = 'educatio_educat') {
        $options = array(
            PDO::ATTR_PERSISTENT => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        try {
            if ($serverID == 'MASTER') {
                $dsn = 'mysql:host=' . constant('MASTER_HOST') . ';dbname=' . $defaultDatabase;
                $user = constant('MASTER_USER');
                $passwd = constant('MASTER_PWD');
            } else if ($serverID == 'SLAVE') {
                $dsn = 'mysql:host=' . constant('SLAVE_HOST') . ';dbname=' . $defaultDatabase;
                $user = constant('SLAVE_USER');
                $passwd = constant('SLAVE_PWD');
            }
            parent::__construct($dsn, $user, $passwd, $options);
            $this->setNewConfiguration();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

    private function setNewConfiguration() {
        /**
         * Two functions called are described in their definition.
         * This function is to be called in the constructor.
         */
        $this->removeMagicQuotesEffect();
        $this->closeMySQLConnection();
    }

    private function removeMagicQuotesEffect() {
        /**
         * Lines below are added to remove the effect of magic quotes on the server.
         * We can remove the code below after disabling magic quotes.
         */
        try {
            $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
            while (list($key, $val) = each($process)) {
                foreach ($val as $k => $v) {
                    unset($process[$key][$k]);
                    if (is_array($v)) {
                        $process[$key][stripslashes($k)] = $v;
                        $process[] = &$process[$key][stripslashes($k)];
                    } else {
                        $process[$key][stripslashes($k)] = stripslashes($v);
                    }
                }
            }
            unset($process);
        } catch (Exception $ex) {
            return false;
        }
    }

    private function closeMySQLConnection() {
        /**
         * This function is needed to close the old library connection.
         * In the migration process server will connect dual ways.
         * 1) MySQL library
         * 2) PDO library
         * So this one will close the older one connection.
         */
        try {
            if (mysql_ping())
                mysql_close();
        } catch (Exception $ex) {
            return false;
        }
    }

    public function debug() {
        if (!empty($this->errorCallbackFunction)) {
            $error = array("Error" => $this->error);
            if (!empty($this->sql))
                $error["SQL Statement"] = $this->sql;
            if (!empty($this->bind))
                $error["Bind Parameters"] = trim(print_r($this->bind, true));

            $backtrace = debug_backtrace();
            if (!empty($backtrace)) {
                foreach ($backtrace as $info) {
                    if ($info["file"] != __FILE__)
                        $error["Backtrace"] = $info["file"] . " at line " . $info["line"];
                }
            }

            $msg = "";
            if ($this->errorMsgFormat == "html") {
                if (!empty($error["Bind Parameters"]))
                    $error["Bind Parameters"] = "<pre>" . $error["Bind Parameters"] . "</pre>";
                $css = trim(file_get_contents(dirname(__FILE__) . "/error.css"));
                $msg .= '<style type="text/css">' . "\n" . $css . "\n</style>";
                $msg .= "\n" . '<div class="db-error">' . "\n\t<h3>SQL Error</h3>";
                foreach ($error as $key => $val)
                    $msg .= "\n\t<label>" . $key . ":</label>" . $val;
                $msg .= "\n\t</div>\n</div>";
            }
            elseif ($this->errorMsgFormat == "text") {
                $msg .= "SQL Error\n" . str_repeat("-", 50);
                foreach ($error as $key => $val)
                    $msg .= "\n\n$key:\n$val";
            }

            $func = $this->errorCallbackFunction;
            $func($msg);
        }
    }

    public function delete($table, $where, $bind = "") {
        $sql = "DELETE FROM " . $table . " WHERE " . $where . ";";
        $this->run($sql, $bind);
    }

    public function localBind($string) {
        return($this->quote($string));
    }

    private function filter($table, $info) {
        $driver = $this->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver == 'sqlite') {
            $sql = "PRAGMA table_info('" . $table . "');";
            $key = "name";
        } elseif ($driver == 'mysql') {
            $sql = "DESCRIBE " . $table . ";";
            $key = "Field";
        } else {
            $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
            $key = "column_name";
        }

        if (false !== ($list = $this->run($sql))) {
            $fields = array();
            foreach ($list as $record)
                $fields[] = $record[$key];
            return array_values(array_intersect($fields, array_keys($info)));
        }
        return array();
    }

    private function cleanup($bind) {
        if (!is_array($bind)) {
            if (!empty($bind))
                $bind = array($bind);
            else
                $bind = array();
        }
        return $bind;
    }

    public function insert($table, $info) {
        $fields = $this->filter($table, $info);
        $sql = "INSERT INTO " . $table . " (" . implode($fields, ", ") . ") VALUES (:" . implode($fields, ", :") . ");";
        $bind = array();
        foreach ($fields as $field)
            $bind[":$field"] = $info[$field];
        return $this->run($sql, $bind);
    }

    public function run($sql, $bind = "") {
        $this->sql = trim($sql);
        $this->bind = $this->cleanup($bind);
        $this->error = "";

        try {
            $pdostmt = $this->prepare($this->sql);
            if ($pdostmt->execute($this->bind) !== false) {
                if (preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->sql))
                    return $pdostmt->fetchAll(PDO::FETCH_ASSOC);
                elseif (preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->sql))
                    return $pdostmt->rowCount();
            }
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            $this->debug();
            return false;
        }
    }

    public function select($table, $where = "", $bind = "", $fields = "*") {
        $sql = "SELECT " . $fields . " FROM " . $table;
        if (!empty($where))
            $sql .= " WHERE " . $where;
        $sql .= ";";
        return $this->run($sql, $bind);
    }

    public function setErrorCallbackFunction($errorCallbackFunction, $errorMsgFormat = "html") {
        //Variable functions for won't work with language constructs such as echo and print, so these are replaced with print_r.
        if (in_array(strtolower($errorCallbackFunction), array("echo", "print")))
            $errorCallbackFunction = "print_r";

        if (function_exists($errorCallbackFunction)) {
            $this->errorCallbackFunction = $errorCallbackFunction;
            if (!in_array(strtolower($errorMsgFormat), array("html", "text")))
                $errorMsgFormat = "html";
            $this->errorMsgFormat = $errorMsgFormat;
        }
    }

    public function update($table, $info, $where, $bind = "") {
        $fields = $this->filter($table, $info);
        $fieldSize = sizeof($fields);

        $sql = "UPDATE " . $table . " SET ";
        for ($f = 0; $f < $fieldSize; ++$f) {
            if ($f > 0)
                $sql .= ", ";
            $sql .= $fields[$f] . " = :update_" . $fields[$f];
        }
        $sql .= " WHERE " . $where . ";";

        $bind = $this->cleanup($bind);
        foreach ($fields as $field)
            $bind[":update_$field"] = $info[$field];

        return $this->run($sql, $bind);
    }

    public function dbErrorReport($msg) {
        echo 'test' . $msg;
        exit;
    }

}

?>

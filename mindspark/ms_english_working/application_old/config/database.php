<?php  

$sep = "/";
    include_once($_SERVER['DOCUMENT_ROOT'].$sep."db_credentials.php");

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
| ['hostname'] The hostname of your database server.
| ['username'] The username used to connect to the database
| ['password'] The password used to connect to the database
| ['database'] The name of the database you want to connect to
| ['dbdriver'] The database type. ie: mysql.  Currently supported:
         mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
| ['dbprefix'] You can add an optional prefix, which will be added
|        to the table name when using the  Active Record class
| ['pconnect'] TRUE/FALSE - Whether to use a persistent connection
| ['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
| ['cache_on'] TRUE/FALSE - Enables/disables query caching
| ['cachedir'] The path to the folder where cache files should be stored
| ['char_set'] The character set used in communicating with the database
| ['dbcollat'] The character collation used in communicating with the database
|        NOTE: For MySQL and MySQLi databases, this setting is only used
|          as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|        (and in table creation queries made with DB Forge).
|          There is an incompatibility in PHP with mysql_real_escape_string() which
|          can make your site vulnerable to SQL injection if you are using a
|          multi-byte character set and are running versions lower than these.
|          Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
| ['swap_pre'] A default table prefix that should be swapped with the dbprefix
| ['autoinit'] Whether or not to automatically initialize the database.
| ['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|             - good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/
$active_group = "mindspark_english";
$active_record = TRUE;
// $db['default']['hostname'] = '27.109.14.76';
// $db['default']['username'] = 'root';
// $db['default']['password'] = '';
// $db['default']['database'] = 'educatio_adepts';
// $db['default']['dbdriver'] = 'mysqli';
// $db['default']['dbprefix'] = '';
// $db['default']['pconnect'] = TRUE;
// $db['default']['db_debug'] = TRUE;
// $db['default']['cache_on'] = FALSE;
// $db['default']['cachedir'] = '';
// $db['default']['char_set'] = 'utf8';
// $db['default']['dbcollat'] = 'utf8_general_ci';
// $db['default']['swap_pre'] = '';
// $db['default']['autoinit'] = TRUE;
// $db['default']['stricton'] = FALSE;

// mindspark english database
$db['mindspark_english']['hostname'] = MASTER_HOST; 
$db['mindspark_english']['username'] = MASTER_USER;
$db['mindspark_english']['password'] = MASTER_PWD;

$db['mindspark_english']['database'] = 'educatio_msenglish';
$db['mindspark_english']['dbdriver'] = 'mysql';
$db['mindspark_english']['dbprefix'] = '';
$db['mindspark_english']['pconnect'] = FALSE;
$db['mindspark_english']['db_debug'] = TRUE;
$db['mindspark_english']['cache_on'] = FALSE;
$db['mindspark_english']['cachedir'] = '';
$db['mindspark_english']['char_set'] = 'utf8';
$db['mindspark_english']['dbcollat'] = 'utf8_general_ci';

$db['mindspark_english_replica']['hostname'] = REPLICA_HOST; 
$db['mindspark_english_replica']['username'] = REPLICA_USER;
$db['mindspark_english_replica']['password'] = REPLICA_PWD;

$db['mindspark_english_replica']['database'] = 'educatio_msenglish';
$db['mindspark_english_replica']['dbdriver'] = 'mysql';
$db['mindspark_english_replica']['dbprefix'] = '';
$db['mindspark_english_replica']['pconnect'] = FALSE;
$db['mindspark_english_replica']['db_debug'] = TRUE;
$db['mindspark_english_replica']['cache_on'] = FALSE;
$db['mindspark_english_replica']['cachedir'] = '';
$db['mindspark_english_replica']['char_set'] = 'utf8';
$db['mindspark_english_replica']['dbcollat'] = 'utf8_general_ci';



$db['database_educat']['hostname'] = MASTER_HOST; 
$db['database_educat']['username'] = MASTER_USER;
$db['database_educat']['password'] = MASTER_PWD;

$db['database_educat']['database'] = 'educatio_educat';
$db['database_educat']['dbdriver'] = 'mysql';
$db['database_educat']['dbprefix'] = '';
$db['database_educat']['pconnect'] = FALSE;
$db['database_educat']['db_debug'] = TRUE;
$db['database_educat']['cache_on'] = FALSE;
$db['database_educat']['cachedir'] = '';
$db['database_educat']['char_set'] = 'utf8';
$db['database_educat']['dbcollat'] = 'utf8_general_ci';

$db['database_adepts']['hostname'] = MASTER_HOST; 
$db['database_adepts']['username'] = MASTER_USER;
$db['database_adepts']['password'] = MASTER_PWD;

$db['database_adepts']['database'] = 'educatio_adepts';
$db['database_adepts']['dbdriver'] = 'mysql';
$db['database_adepts']['dbprefix'] = '';
$db['database_adepts']['pconnect'] = FALSE;
$db['database_adepts']['db_debug'] = TRUE;
$db['database_adepts']['cache_on'] = FALSE;
$db['database_adepts']['cachedir'] = '';
$db['database_adepts']['char_set'] = 'utf8';
$db['database_adepts']['dbcollat'] = 'utf8_general_ci';


// Education Initiative database
// $db['database_educat']['hostname'] = '27.109.14.76'; 
// $db['database_educat']['username'] = 'root';
// $db['database_educat']['password'] = '';
// $db['database_educat']['database'] = 'educatio_educat';
// $db['database_educat']['dbdriver'] = 'mysql';
// $db['database_educat']['dbprefix'] = '';
// $db['database_educat']['pconnect'] = FALSE;
// $db['database_educat']['db_debug'] = TRUE;
// $db['database_educat']['cache_on'] = FALSE;
// $db['database_educat']['cachedir'] = '';
// $db['database_educat']['char_set'] = 'utf8';
// $db['database_educat']['dbcollat'] = 'utf8_general_ci';

/* use in model

class Anotherdb_model extends CI_Model
{
  private $another;
  function __construct()
  {
    parent::__construct();
    $this->another = $this->load->database('educatio_educat',TRUE);
  }
 
  public function getSomething()
  {
    $this->another->select('somecol');
    $q = $this->another->get('sometable');
    if($q->num_rows()>0)
    {
      foreach($q->result() as $row)
      {
        $data[] = $row;
      }
    }
    else
    {
      return FALSE;
    }
  }
}*/
/* End of file database.php */
/* Location: ./application/config/database.php */
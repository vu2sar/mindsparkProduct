<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keys Controller
 *
 * This is a basic Key Management REST controller to make and delete keys.
 * Company: EI-INDIA
 * Code Author Amit Kumar Varshney 
 */
// This can be removed if you use __autoload() in config.php
require_once(APPPATH . '/libraries/REST_Controller.php');

class Key extends REST_Controller {

    protected $methods = array(
        'index_post' => array('level' => 2, 'limit' => 10),
        'index_delete' => array('level' => 2),
        'level_post' => array('level' => 2),
        'regenerate_post' => array('level' => 2),
    );

    /**
     * Key Create
     *
     * Insert a key into the database.
     *
     * @access	public
     * @return	void
     */
     
    public function index_post() {
       
        // Build a new key
        $key = self::_generate_key();
        $level = 11;
        $ignore_limits = $this->put('ignore_limits') ? $this->put('ignore_limits') : 1;

        // Insert the new key
        if (self::_insert_key($key, array('level' => $level, 'ignore_limits' => $ignore_limits))) {
            $this->response(array('eiCode' => 1, 'key' => $key), 200); // 201 = Created
        } else {
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Could not save the key.'), 400); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Key Delete
     *
     * Remove a key from the database to stop it working.
     *
     * @access	public
     * @return	void
     */
    public function index_delete($key) {
        //$key = $this->delete('key');

        // Does this key even exist?
        if (!self::_key_exists($key)) {
            // NOOOOOOOOO!
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Invalid API Key.'), 400);
        }

        // Kill it
        self::_delete_key($key);

        // Tell em we killed it
        $this->response(array('eiCode' => 1, 'eiMsg' => 'API Key was deleted.'), 200);
    }

    // --------------------------------------------------------------------

    /**
     * Update Key
     *
     * Change the level
     *
     * @access	public
     * @return	void
     */
    public function level_post() {
        $key = $this->post('key');
        $new_level = $this->post('level');

        // Does this key even exist?
        if (!self::_key_exists($key)) {
            // NOOOOOOOOO!
            $this->response(array('error' => 'Invalid API Key.'), 400);
        }

        // Update the key level
        if (self::_update_key($key, array('level' => $new_level))) {
            $this->response(array('eiCode' => 1, 'eiMsg' => 'API Key was updated.'), 200); // 200 = OK
        } else {
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Could not update the key level.'), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Update Key
     *
     * Change the level
     *
     * @access	public
     * @return	void
     */
    public function suspend_post() {
        $key = $this->post('key');

        // Does this key even exist?
        if (!self::_key_exists($key)) {
            // NOOOOOOOOO!
            $this->response(array('error' => 'Invalid API Key.'), 400);
        }

        // Update the key level
        if (self::_update_key($key, array('level' => 0))) {
            $this->response(array('eiCode' => 1, 'eiMsg' => 'Key was suspended.'), 200); // 200 = OK
        } else {
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Could not suspend the user.'), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Regenerate Key
     *
     * Remove a key from the database to stop it working.
     *
     * @access	public
     * @return	void
     */
    public function regenerate_post() {
        $old_key = $this->post('key');
        $key_details = self::_get_key($old_key);

        // The key wasnt found
        if (!$key_details) {
            // NOOOOOOOOO!
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Invalid API Key.'), 400);
        }

        // Build a new key
        $new_key = self::_generate_key();

        // Insert the new key
        if (self::_insert_key($new_key, array('level' => $key_details->level, 'ignore_limits' => $key_details->ignore_limits))) {
            // Suspend old key
            self::_update_key($old_key, array('level' => 0));

            $this->response(array('eiCode' => 1, 'key' => $new_key), 201); // 201 = Created
        } else {
            $this->response(array('eiCode' => 0, 'eiMsg' => 'Could not save the key.'), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /* Helper Methods */

    private function _generate_key() {
        

        do {
            $salt = do_hash(time() . mt_rand());
            $new_key = substr($salt, 0, config_item('rest_key_length'));
        }

        // Already in the DB? Fail. Try again
        while (self::_key_exists($new_key));

        return $new_key;
    }

    // --------------------------------------------------------------------

    /* Private Data Methods */

    private function _get_key($key) {
        return $this->db->where(config_item('rest_key_column'), $key)->get(config_item('rest_keys_table'))->row();
    }

    // --------------------------------------------------------------------

    private function _key_exists($key) {
        return $this->db->where(config_item('rest_key_column'), $key)->count_all_results(config_item('rest_keys_table')) > 0;
    }

    // --------------------------------------------------------------------

    private function _insert_key($key, $data) {

        $data[config_item('rest_key_column')] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();

        return $this->db->set($data)->insert(config_item('rest_keys_table'));
    }

    // --------------------------------------------------------------------

    private function _update_key($key, $data) {
        return $this->db->where(config_item('rest_key_column'), $key)->update(config_item('rest_keys_table'), $data);
    }

    // --------------------------------------------------------------------

    private function _delete_key($key) {
        return $this->db->where(config_item('rest_key_column'), $key)->delete(config_item('rest_keys_table'));
    }

} 
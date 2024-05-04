<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * An extension of the base CodeIgniter model that provides a number of handy CRUD
 * functions, validation, relationships, observers and more.
 *
 * @link http://github.com/dwightwatson/codeigniter-model
 */
class MY_Model extends CI_Model
{
    /**
     * The table name.
     */
    protected $_table;

    /**
     * Define field properties.
     */
    protected $_primary_key = 'id';
    protected $_fields = array();

    /**
     * Define field protection functionality.
     */
    protected $_protect_fields = TRUE;
    protected $_protected_fields;

    /**
     * Define soft delete functionality.
     */
    protected $_soft_delete = FALSE;
    protected $_soft_delete_key = 'deleted';
    protected $_include_deleted = FALSE;

    /**
     * Define model relationships.
     */
    protected $_has_one = array();
    protected $_has_many = array();
    protected $_has_and_belongs_to_many = array();
    protected $_belongs_to = array();

    /**
     * Define model validation rules.
     */
    protected $_validation_rules = array();
    protected $_skip_validation = FALSE;

    /**
     * Define model filters.
     */
    protected $_before_create = array();
    protected $_after_create = array();
    protected $_before_read = array();
    protected $_after_read = array();
    protected $_before_update = array();
    protected $_after_update = array();
    protected $_before_delete = array();
    protected $_after_delete = array();
    protected $_callback_parameters = array();

    /**
     * By default results are returned as objects, but this can be overridden.
     */
    protected $_return_type = 'object';

    public function __construct()
    {
        parent::__construct();

        // Ensure database library is loaded.
        if (!$this->db) {
            $this->load->library('database');
        }

        // Get the model table.
        $this->_determine_table();

        // Get the model fields.
        $this->_determine_fields();

        // If field protection is enabled, add the protect_attributes
        // function to be run as a filter prior to creates and updates.
        if ($this->_protect_fields) {
            array_unshift($this->_before_create, 'protect_attributes');
            array_unshift($this->_before_update, 'protect_attributes');
        }
    }

    /**
     * Catch-all function to capture method requests to achieve
     * ActiveRecord-style functionality.
     */
    public function __call($method, $arguments)
    {
        log_message('debug', 'MY_Model captured function call.');

        // Capture methods as get_by_*
        if (stristr($method, 'get_by')) {
            return $this->_magic_get_by(str_replace('get_by_', '', $method), $arguments);
        }

        // Capture methods as get_all_by_*
        if (stristr($method, 'get_all_by')) {
            return $this->_get_all_by(str_replace('get_all_by_', '', $method), $arguments);
        }

        log_message('debug', 'MY_Model now looking for potential relationships.');

        if (array_key_exists($method, $this->_has_one)) {
            // Implement has_one relationship handling.
        }

        if (array_key_exists($method, $this->_has_many)) {
            // Implement has_many relationship handling.
        }

        if (array_key_exists($method, $this->_has_and_belongs_to_many)) {
            // Implement has_and_belongs_to_many relationship handling.
        }

        if (array_key_exists($method, $this->_belongs_to)) {
            // Implement belongs_to relationship handling.
        }

        log_message('debug', 'MY_Model passing the function call onto the model itself.');

        // Check if arguments are provided and call the method accordingly.
        if (isset($args)) {
            return $this->$method;
        }

        return $this->$method($args);
    }

    /**
     * Determines the table of the model by removing the _model suffix,
     * making the name lowercase and then pluralising it.
     *
     * For example, User_model maps to the table users.
     */
    private function _determine_table()
    {
        if (!isset($this->_table)) {
            $this->load->helper('inflector');
            $this->_table = plural(strtolower(str_replace('_model', '', get_class($this))));
        }
    }

    /**
     * Determines the fields of the table using the database tools.
     */
    private function _determine_fields()
    {
        if (!isset($this->_fields)) {
            $this->_fields = $this->db->list_fields($this->_table);
        }
    }

    /* -------------------------------------
     * CREATE FUNCTIONS
     * ---------------------------------- */

    /**
     * Creates an item in the database table.
     */
    public function insert($data)
    {
        $valid = TRUE;
        if ($this->_skip_validation === FALSE) {
            $data = $this->_validate($data);
        }
        if ($data !== FALSE) {
            $this->_trigger('before_create', $data);

            $insert_data = array();

            foreach ($this->_fields as $field) {
                if (!array_key_exists($field, $this->_protected_fields)) {
                    if (isset($data[$field])) {
                        $insert_data[$field] = $data[$field];
                    }
                }
            }

            $this->db->insert($this->_table, $insert_data);
            $insert_id = $this->db->insert_id();

            $this->_trigger('after_create', $insert_id);

            return $insert_id;
        } else {
            return FALSE;
        }
    }

    /**
     * Creates multiple items in the database table.
     */
    public function insert_many($data)
    {
        $ids = array();

        foreach ($data as $key => $value) {
            $ids[] = $this->insert($value);
        }

        return $ids;
    }

    /* -------------------------------------
     * READ FUNCTIONS
     * ---------------------------------- */

    // Implement get(), get_by(), get_all_by() methods here.

    /* -------------------------------------
     * UPDATE FUNCTIONS
     * ---------------------------------- */

    // Implement update(), update_many(), update_by(), update_all() methods here.

    /* -------------------------------------
     * DELETE FUNCTIONS
     * ---------------------------------- */

    // Implement delete(), delete_many(), delete_by() methods here.

    /* -------------------------------------
     * UTILITY FUNCTIONS
     * ---------------------------------- */

    // Implement count_all(), get_next_id(), get_table(), include_deleted() methods here.

    /* -------------------------------------
     * OBSERVERS
     * ---------------------------------- */

    /**
     * Adds the current DATETIME to the 'created' property of a row.
     */
    public function created($row)
    {
        if (is_object($row)) {
            $row->created = date('Y-m-d H:i:s');
        } else {
            $row['created'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Adds the current DATETIME to the 'updated' property of a row.
     */
    public function updated($row)
    {
        if (is_object($row)) {
            $row->updated = date('Y-m-d H:i:s');
        } else {
            $row['updated'] = date('Y-m-d H:i:s');
        }

        return $row;
    }

    /**
     * Removes protected attributes from a row.
     */
    public function protect_attributes($row)
    {
        foreach ($this->_protected_fields as $attribute) {
            if (is_object($row)) {
                unset($row->$attribute);
            } else if (is_array($row)) {
                unset($row[$attribute]);
            }
        }

        return $row;
    }

    /* -------------------------------------
     * QUERY BUILDER PASS-THROUGH METHODS
     * ---------------------------------- */

    // Implement select(), order_by(), limit(), like() methods here.

    /* -------------------------------------
     * INTERNAL FUNCTIONS
     * ---------------------------------- */

    /**
     * If validation is turned on, this will run the validation rules
     * provided against the given data.
     */
    private function _validate($data)
    {
        // If we're meant to skip validation, we'll just return the given data.
        if ($this->_skip_validation) {
            return $data;
        }
        // Only run the rules if they actually exist.
        if (!empty($this->_validation_rules)) {
            // We need to put the data back into the $_POST global so that
            // the CodeIgniter form validation library can process it.
            foreach ($data as $key => $value) {
                $_POST[$key] = $value;
            }
            $this->load->library('form_validation');
            if (is_array($this->_validation_rules)) {
                $this->form_validation->set_rules($this->_validation_rules);
                if ($this->form_validation->run() === TRUE) {
                    return $data;
                } else {
                    return FALSE;
                }
            } else {
                if ($this->form_validation->run($this->_validation_rules) === TRUE) {
                    return $data;
                } else {
                    return FALSE;
                }
            }
        } else {
            return $data;
        }
    }

    /**
     * Trigger an event and call its observers.
     */
    private function _trigger($event, $data = FALSE, $last = TRUE)
    {
        if (isset($this->{'_' . $event}) && is_array($this->{'_' . $event})) {
            foreach ($this->{'_' . $event} as $method) {
                if (strpos($method, '(')) {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

                    $method = $matches[1];
                    $this->_callback_parameters = explode(',', $matches[3]);
                }

                $data = call_user_func_array(array($this, $method), array($data, $last));
            }
        }

        return $data;
    }
}

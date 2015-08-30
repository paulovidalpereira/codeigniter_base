<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $_table;
    protected $_modelObject;
    protected $_primarykey = 'id';

    protected $_validate = array();
    protected $_skip_validation = FALSE;

    /**
     * Atributos protegidos
     * Ex: $protected_attributes = array('id','hash');
     */
    protected $_protected_attributes = array();

    protected $_data = FALSE;
    protected $_result = FALSE;

    protected $_num_rows = 0;

    protected $_page_size = 20;
    protected $_cur_page = 1;

    protected $_before_get = array();
    protected $_after_get = array();
    protected $_before_insert = array();
    protected $_after_insert = array();
    protected $_before_update = array();
    protected $_after_update = array();
    protected $_before_delete = array();
    protected $_after_delete = array();

    protected $_callback_parameters = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function __call($method,$args)
    {
        switch (substr($method, 0, 4))
        {
            case 'get_':
                $key = substr($method,4);
                $data = isset($this->_data[$key]) ? $this->_data[$key] : NULL;

                return $data;

            case 'set_':
                $key = substr($method,4);
                $this->_data[$key]= isset($args[0]) ? $args[0] : NULL;

                return $this;
        }

    }

    /**
     * Retorna registros
     */
    public function get_collection()
    {
        $this->trigger('_before_get');

        // $this->db->select($this->_table.'.*');
        $this->db->from($this->_table);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        $result_object = array();
        $class_name = $this->_modelObject;

        if( $num_rows > 0 )
        {
            $result = $query->result_array();

            foreach ( $result as $row )
            {
                $row = $this->trigger('_after_get', $row);

                $object = new $class_name();
                $object->_data = $row;

                $result_object[] = $object;
            }

            $this->_num_rows = $num_rows;
            $this->_result = $result_object;
        }

        return $this;
    }

    public function get_pagination()
    {
        $this->trigger('_before_get');

        $this->db->from($this->_table);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        $result_object = array();
        $class_name = $this->_modelObject;

        if( $num_rows > 0 )
        {
            $result = $query->result_array();

            foreach ( $result as $row )
            {
                $row = $this->trigger('_after_get', $row);

                $object = new $class_name();
                $object->_data = $row;

                $result_object[] = $object;
            }

            $this->_num_rows = $num_rows;
            $this->_result = $result_object;
        }

        return $this;
    }

    public function get_row()
    {
        $this->trigger('_before_get');

        $this->db
            ->from($this->_table)
            ->limit(1);

        $query = $this->db->get();
        $num_rows = $query->num_rows();
        $class_name = $this->_modelObject;

        if( $num_rows > 0 )
        {
            $row = $query->row_array();
            $row = $this->trigger('_after_get', $row);

            $this->_data = $row;

            return $this;
        }

        return FALSE;
    }

    public function load($id)
    {
        $this->db
            ->where($this->_primarykey,$id);

        $row = $this->get_row();

        return $row;
    }

    public function load_by_attribute($attribute, $value)
    {
        $this->add_attribute_to_filter($attribute, $value);

        return $this->get_row();
    }

    public function count()
    {
        $this->db->from($this->_table);

        $count = $this->db->count_all_results();

        return $count;
    }

    //---------------------------------------------------------------

    public function get_result()
    {
        return $this->_result;
    }

    public function get_num_rows()
    {
        return $this->_num_rows;
    }

    //---------------------------------------------------------------

    public function set_data($key, $value)
    {
        $this->_data[$key] = $value;

        return $this;
    }

    public function get_data($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : NULL;
    }

    public function get_date($date)
    {
        $dt = new DateTime($date);

        return $dt;
    }

    public function get_id()
    {
        return $this->get_data($this->_primarykey);
    }

    public function get_criado_em()
    {
        return $this->get_date($this->get_data('criado_em'));
    }

    public function get_atualizado_em()
    {
        return $this->get_date($this->get_data('atualizado_em'));
    }

    //---------------------------------------------------------------

    public function save()
    {
        if( isset($this->_data[$this->_primarykey]) )
        {
            return $this->_update();
        }
        else
        {
            return $this->_insert();
        }

        return FALSE;
    }

    public function delete()
    {
        $this->trigger('_before_delete');

        $result = $this->db->delete($this->_table,array($this->_primarykey => $this->get_data($this->_primarykey)));

        $this->trigger('_after_delete', $result);

        return $result;
    }

    protected function _insert()
    {
        $this->trigger('_before_insert');

        $this->db->set($this->_data);
        $this->db->insert($this->_table);

        $insert_id = $this->db->insert_id();

        $return = $this->trigger('_after_insert', $insert_id);

        return $return;
    }

    protected function _update()
    {
        $this->trigger('_before_update');

        $this->db->set($this->_data);
        $this->db->where($this->_primarykey, $this->get_data($this->_primarykey));

        $result = $this->db->update($this->_table);

        $this->trigger('_after_update', $result);

        return $result;
    }

    //---------------------------------------------------------------

    public function add_attribute_to_select($attribute)
    {
        $this->db
            ->select($attribute);

        return $this;
    }

    public function add_attribute_to_filter($field, $condition = NULL)
    {
        if( $condition == NULL )
        {
            $this->db
                ->where($field);
        }
        elseif( ! is_array($condition) )
        {
            $this->db
                ->where($field, $condition);
        }
        else
        {
            switch (key($condition)) {
                case 'like':
                    $this->db
                        ->like($field, $condition['like']);
                    break;
                case 'where':
                    $this->db
                        ->where($field, $condition['where']);
                    break;
            }

        }

        return $this;
    }

    public function add_is_active_filter()
    {
        $this->db
            ->where('ativo',1);

        return $this;
    }

    public function join_field($table,$where)
    {
        $this->db->join($table,$where);

        return $this;
    }

    public function set_order($field, $order)
    {
        $this->db->order_by($field,$order);

        return $this;
    }

    public function set_page_size($size)
    {
        $this->_page_size = $size;

        return $this;
    }

    public function set_cur_page($page)
    {
        $this->_cur_page = $page;

        return $this;
    }

    public function set_limit($offset,$limit)
    {
        $this->db->limit($offset,$limit);

        return $this;
    }

    //---------------------------------------------------------------

    public function trigger($event, $data = FALSE, $last = TRUE)
    {
        if (isset($this->$event) && is_array($this->$event))
        {
            foreach ($this->$event as $method)
            {
                if (strpos($method, '('))
                {
                    preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);
                    $method = $matches[1];
                    $this->_callback_parameters = explode(',', $matches[3]);
                }

                if( method_exists($this,$method) )
                {
                    $data = call_user_func_array(array($this, $method), array($data, $last));
                }

            }
        }

        return $data;
    }

    public function get_sql()
    {
        return $this->db->get_compiled_select(null,false);
    }

}
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
    protected $protected_attributes = array();

    protected $_result = FALSE;
    protected $_num_rows = 0;
    protected $_page_size = 20;
    protected $_cur_page = 1;

    protected $before_save = array();
    protected $after_save = array();
    protected $before_update = array();
    protected $after_update = array();
    protected $before_get = array();
    protected $after_get = array();
    protected $before_delete = array();
    protected $after_delete = array();

    protected $callback_parameters = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('inflector');
    }

    public function __call($method,$args)
    {
        switch (substr($method, 0, 4))
        {
            case 'get_':
                $key = substr($method,4);
                $data = isset($this->$key) ? $this->$key : NULL;

                return $data;

            case 'set_':
                $key = substr($method,4);
                $this->key = isset($args[0]) ? $args[0] : NULL;
        }

    }

    public function get_all()
    {
        $this->db->from($this->_table);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            $result = $query->result($this->_modelObject);

            $this->_num_rows = $num_rows;
            $this->_result = $result;
        }

        return $this;
    }

    public function get_collection()
    {
        $this->db->from($this->_table);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            $result = $query->result($this->_modelObject);

            $this->_num_rows = $num_rows;
            $this->_result = $result;
        }

        return $this;  
    }

    public function get_row()
    {
        $this->db
            ->from($this->_table)
            ->limit(1);

        $query = $this->db->get();
        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            $this->trigger('before_get');

            $row = $query->row(0,$this->_modelObject);

            $row = $this->trigger('after_get', $row);

            return $row;
        }

        return FALSE;
    }

    public function load($id = NULL)
    {
        if( ! is_null($id) )
        {
            $this->db
                ->where($this->_primarykey,$id);
        }

        $row =  $this->get_row();

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

    public function get_result()
    {
        return $this->_result;
    }

    public function get_num_rows()
    {
        return $this->_num_rows;
    }

    //---------------------------------------------------------------

    public function get_id()
    {
        return $this->{$this->_primarykey};
    }

    public function get_creado_em()
    {
        return new DateTime($this->get_data('creado_em'));
    }

    public function get_atualizado_em()
    {
        return new DateTime($this->get_data('atualizado_em'));
    }

    public function get_data($key)
    {
        return isset($this->$key) ? $this->$key : NULL;
    }

    //---------------------------------------------------------------

    public function save($data)
    {
        if( count($data) )
        {
            $data = $this->trigger('before_save', $data);

            $this->db->insert($this->_table,$data);
            $insert_id = $this->db->insert_id();

            $this->trigger('after_save', $insert_id);

            return $insert_id;
        }

        return FALSE;
    }

    public function update($data)
    {
        $data = $this->trigger('before_update', $data);

        $result = $this->db->update($this->_table,$data,array($this->_primarykey => $this->{$this->_primarykey}));

        $this->trigger('after_update', array($data, $result));

        return $result;
    }

    public function delete()
    {
        return $this->db->delete($this->_table,array($this->_primarykey => $this->{$this->_primarykey}));
    }

    //---------------------------------------------------------------

    public function add_attribute_to_select()
    {
        $this->db
            ->select('*');

        return $this;
    }

    public function add_attribute_to_filter($field, $condition = NULL)
    {
        if( ! is_array($condition) )
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

    public function set_order($field = 'id', $order = 'ASC')
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
                    $this->callback_parameters = explode(',', $matches[3]);
                }

                if( method_exists($this,$method) )
                {
                    $data = call_user_func_array(array($this, $method), array($data, $last));
                }

            }
        }

        return $data;
    }

}
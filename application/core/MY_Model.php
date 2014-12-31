<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $_table;
    protected $_modelObject;

    protected $_validate = array();
    protected $_skip_validation = FALSE;

    private $_result = FALSE;
    private $_num_rows = 0;
    private $_page_size = 20;
    private $_cur_page = 1;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('inflector');
    }

    public function get_all()
    {
        $this->db->from($this->_table);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            $this->_num_rows = $num_rows;
            $this->_result = $query->result($this->_modelObject);
        }

        return $this;
    }

    public function get_collection()
    {
        $this->db->from($this->_table);
        // $this->db->limit($this->_page_size,$this->_cur_page);

        $query = $this->db->get();

        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            $_result = $query->result($this->_modelObject);

            $this->_num_rows = $num_rows;
            $this->_result = $_result;
        }

        return $this;  
    }

    public function count()
    {
        $this->db->from($this->_table);

        $count = $this->db->count_all_results();

        return $count;
    }

    public function get_row()
    {
        $this->db->from($this->_table);

        $query = $this->db->get();
        $num_rows = $query->num_rows();

        if( $num_rows > 0 )
        {
            return $query->row(0,$this->_modelObject);
        }

        return FALSE;
    }

    public function get_result()
    {
        return $this->_result;
    }

    public function get_num_rows()
    {
        return $this->_num_rows;
    }

    public function load($id = NULL)
    {
        if( ! is_null($id) )
        {
            $this->db
                ->where($this->_primarykey,$id);
        }

        $this->db
            ->limit(1);

        return $this->getRow();
    }

    public function get_id()
    {
        $primarykey = $this->_primarykey;
        return $this->$primarykey;
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

    public function load_by_atributo($attribute, $value)
    {
        $this->addAttributeToFilter($attribute, $value);

        return $this->getRow();
    }

    public function save($data = array() ,$id = NULL)
    {
        if( $id === NULL && isset($this->id) === FALSE )
        {
            $query = $this->db->insert($this->_table,$data);
            return $this->db->insert_id();
        }
        else
        {
            if( isset($this->id) )
            {
                $id = $this->id;
            }

            return $this->db->update($this->_table,$data,array($this->_primarykey => $id));
        }

        return FALSE;
    }

    public function delete()
    {
        return $this->db->delete($this->_table,array($this->_primarykey => $this->id));
    }

    public function update($data = array() ,$id)
    {
        return $this->db->update($this->_table,$data,array($this->_primarykey => $id));
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

    public function add_atributo_to_select()
    {
        $this->db
            ->select('*');

        return $this;
    }

    public function add_atributo_to_filter($field, $condition = NULL)
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

    public function add_is_ativo_filter()
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

}
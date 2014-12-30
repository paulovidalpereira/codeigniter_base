<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected $_modelObject = NULL;

    private $_result = FALSE;
    private $_num_rows = 0;
    private $_page_size = 20;
    private $_cur_page = 1;

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
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

    public function getCollection()
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

    public function getRow()
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

    public function getResult()
    {
        return $this->_result;
    }

    public function getNumRows()
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

    public function getId()
    {
        $primarykey = $this->_primarykey;
        return $this->$primarykey;
    }

    public function getCreadoEm()
    {
        return new DateTime($this->getData('creado_em'));
    }

    public function getAtualizadoEm()
    {
        return new DateTime($this->getData('atualizado_em'));
    }

    public function getData($key)
    {
        return isset($this->$key) ? $this->$key : NULL;
    }

    public function loadByAttribute($attribute, $value)
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

    public function __call($method,$args)
    {
        switch (substr($method, 0, 3))
        {
            case 'get':
                $key = $this->_underscore(substr($method,3));
                $data = isset($this->$key) ? $this->$key : NULL;

                return $data;

            case 'set':
                $key = $this->_underscore(substr($method,3));
                $this->key = isset($args[0]) ? $args[0] : NULL;
        }

    }

    public function _underscore($name, $uppercase = FALSE)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));

        if( $uppercase )
        {
            $result = strtoupper($result);
        }

        return $result;
    }

    public function _camelize($name)
    {
        return $this->_uc_words($name, '');
    }

    public function _uc_words($str, $destSep = '_', $srcSep = '_')
    {
        return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
    }

    public function addAttributeToSelect()
    {
        $this->db
            ->select('*');

        return $this;
    }

    public function addAttributeToFilter($field, $condition = NULL)
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

    public function addIsActiveFilter()
    {
        $this->db
            ->where('active',1);

        return $this;
    }

    public function joinField($table,$where)
    {
        $this->db->join($table,$where);

        return $this;
    }

    public function setOrder($field = 'id', $order = 'ASC')
    {
        $this->db->order_by($field,$order);

        return $this;
    }

    public function setPageSize($size)
    {
        $this->_page_size = $size;

        return $this;
    }

    public function setCurPage($page)
    {
        $this->_cur_page = $page;

        return $this;
    }

    public function setLimit($offset,$limit)
    {
        $this->db->limit($offset,$limit);

        return $this;
    }

}
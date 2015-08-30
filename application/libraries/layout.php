<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Layout extends Object {

    private $_class_name;
    private $_method_name;

    private $_css_files = array();
    private $_js_files = array();
    private $_js_head_files = array();

    private $_package = 'frontend';

    private $_titulo = '';

    public function __construct()
    {       
        $this->_class = $this->router->fetch_directory() . $this->router->fetch_class();
        $this->_method = $this->router->fetch_method();
    }

    public function view($file)
    {
        $this->load->view($this->get_package().'/'.$file);
    }

    public function render()
    {
        $this->load->vars($this->_data);
        $this->load->view($this->get_package().'/'.$this->get_class().'/'.$this->get_method());
    }

    public function add_item($file, $type, $package = null, $head = false)
    {
        if( $type == 'css' )
        {
            if( ! strpos($file,'.css') )
            {
                $file = $file.'.css';
            }

            $this->_css_files[] = site_url('skin/'.( ($package == null )? $this->get_package() : $package ).'/'.$file);

        }
        elseif( $type == 'js' )
        {
            if( ! strpos($file,'.js') )
            {
                $file = $file.'.js';
            }

            if( ! $head )
            {
                $this->_js_files[] = site_url('skin/'.( ($package == null )? $this->get_package() : $package ).'/'.$file);
            }
            else
            {
                $this->_js_head_files[] = site_url('skin/'.( ($package == null )? $this->get_package() : $package ).'/'.$file);
            }
        }
    }

    /**
     * Adiciona CSS
     */
    public function add_css($file)
    {
        $this->add_item('css/'.$file,'css');
    }

    /**
     * Renderiza Linhas Css
     */
    public function render_css()
    {
        $output = '';

        if( count($this->_css_files) )
        {
            foreach ($this->_css_files as $file)
            {
                $output .= '<link rel="stylesheet" href="'.$file.'" media="all" />';
            }
        }

        return $output;
    }

    /**
     * Adiciona JS
     */
    public function add_js($file, $head = false)
    {
        $this->add_item('js/'.$file,'js',null,$head);
    }

    /**
     * Renderiza Linhas Js
     */
    public function render_js($head = false)
    {
        $output = '';

        if( $head )
        {
            $files = $this->_js_head_files;
        }
        else
        {
            $files = $this->_js_files;
        }

        if( count($files) )
        {
            foreach ($files as $file)
            {
                $output .= '<script src="'.$file.'"></script>';
            }

        }

        return $output;
    }

    public function get_class()
    {
        return $this->_class;
    }

    public function get_method()
    {
        return $this->_method;
    }

    public function set_package($package)
    {
        return $this->_package = $package;
    }

    public function get_package()
    {
        return $this->_package;
    }

    public function set_titulo($titulo)
    {
        $this->_titulo = $titulo;
    }

    public function get_titulo()
    {
        return $this->_titulo;
    }

}
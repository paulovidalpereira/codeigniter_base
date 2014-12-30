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
        $this->load->view($this->getPackage().'/'.$file);
    }

    public function render()
    {
        $this->load->vars($this->_data);
        $this->load->view($this->getPackage().'/'.$this->getClass().'/'.$this->getMethod());
    }

    public function addItem($file, $type, $package = null, $head = false)
    {
        if( $type == 'css' )
        {
            if( ! strpos($file,'.css') )
            {
                $file = $file.'.css';
            }

            $this->_css_files[] = site_url('skin/'.( ($package == null )? $this->getPackage() : $package ).'/'.$file);

        }
        elseif( $type == 'js' )
        {
            if( ! strpos($file,'.js') )
            {
                $file = $file.'.js';
            }

            if( ! $head )
            {
                $this->_js_files[] = site_url('skin/'.( ($package == null )? $this->getPackage() : $package ).'/'.$file);
            }
            else
            {
                $this->_js_head_files[] = site_url('skin/'.( ($package == null )? $this->getPackage() : $package ).'/'.$file);
            }
        }
    }

    /**
     * Adiciona CSS
     */
    public function addCss($file)
    {
        $this->addItem('css/'.$file,'css');
    }

    /**
     * Renderiza Linhas Css
     */
    public function renderCss()
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
    public function addJs($file, $head = false)
    {
        $this->addItem('js/'.$file,'js',null,$head);
    }

    /**
     * Renderiza Linhas Js
     */
    public function renderJs($head = false)
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

    public function getClass()
    {
        return $this->_class;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setPackage($package)
    {
        return $this->_package = $package;
    }

    public function getPackage()
    {
        return $this->_package;
    }

    public function setTitulo($titulo)
    {
        $this->_titulo = $titulo;
    }

    public function getTitulo()
    {
        return $this->_titulo;
    }

}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Light extends CI_Controller {

	public $_data = array();

	public function __construct()
	{
		parent::__construct();
	}

	public function get_Url($link)
	{
		return site_url($link);
	}

	public function get_skin_url($file)
	{
		return site_url('skin/'.$this->layout->getLayout().'/'.$file);
	}

	public function get_singleton($class)
	{
		return $this->$class;
	}

	public function get_model($model, $sufixo = '_model')
	{	
		$class = $model.$sufixo;
		
		return new $class;
	}

	public function set_data($key,$value)
	{
		$this->_data[$key] = $value;
	}

	public function get_data($key)
	{
		return $this->_data[$key];
	}

	public function format_date($date, $format = 'd/m/Y', $timezone = 'America/Sao_Paulo')
	{
		return date($format,strtotime($date));
	}

}
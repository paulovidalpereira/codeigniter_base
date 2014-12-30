<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Light extends CI_Controller {

	public $_data = array();

	public function __construct()
	{
		parent::__construct();
	}

	public function getUrl($link)
	{
		return site_url($link);
	}

	public function getSkinUrl($file)
	{
		return site_url('skin/'.$this->layout->getLayout().'/'.$file);
	}

	public function getSingleton($class)
	{
		return $this->$class;
	}

	public function getModel($model)
	{
		return new $model;
	}

	public function setData($key,$value)
	{
		$this->_data[$key] = $value;
	}

	public function getData($key)
	{
		return $this->_data[$key];
	}

	public function formatDate($date, $format = 'd/m/Y')
	{
		return date($format,strtotime($date));
	}

}
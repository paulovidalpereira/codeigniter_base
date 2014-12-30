<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Router extends CI_Router {

	public function __construct()
	{
		parent::__construct();
	}
	
	public function _set_request($segments = array())
	{
		parent::_set_request(str_replace('-', '_', $segments));   
	}

}
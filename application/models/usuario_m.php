<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_m extends MY_Model {

	protected $_table = 'usuarios';
    protected $_modelObject = 'Usuario_m';
    protected $_primarykey = 'id';

	public function __construct()
	{
		parent::__construct();
	}

}
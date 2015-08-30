<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Navegacao extends Object {

	private $_menus = array();

	public function add_menu($titulo, $url = null, $icone = NULL, $active = NULL)
	{
		$this->_menus[$url] = array('titulo' => $titulo, 'url' => $url, 'icone' => $icone, 'active' => ($this->uri->uri_string() == $url)? TRUE: FALSE);
	}

	public function add_submenu()
	{
		# code...
	}

	public function set_active_menu($value='')
	{
		# code...
	}

	public function render()
	{
		sort($this->_menus);
		return $this->load->view('default/page/navegacao',array('menus' => $this->_menus),true);
	}

}
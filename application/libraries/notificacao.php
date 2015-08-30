<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notificacao {

	const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const SUCCESS   = 'success';
    const INFO   	= 'info';

	protected $_mensagens = array();
	protected $_flash_mensagens = array();

	public function __construct()
	{
		$ci =& get_instance();

		$this->_mensagens = $ci->session->flashdata('_mensagens');
	}

	private function _add_mensage($texto, $codigo, $flash = FALSE )
	{
		if( $flash )
		{
			$ci =& get_instance();
			$this->_mensagens[] = array('texto' => $texto, 'codigo' => $codigo);
			$ci->session->set_flashdata('_mensagens', $this->_mensagens);
		}
		else
		{
			$this->_mensagens[] = array('texto' => $texto, 'codigo' => $codigo);
		}
	}

	public function add_error($texto, $flash = FALSE)
	{
		$this->_add_mensage($texto, self::ERROR, $flash);
	}

	public function add_warning($texto, $flash = FALSE)
	{
		$this->_add_mensage($texto, self::WARNING, $flash);
	}

	public function add_notice($texto, $flash = FALSE)
	{
		$this->_add_mensage($texto, self::NOTICE, $flash);
	}

	public function add_success($texto, $flash = FALSE)
	{
		$this->_add_mensage($texto, self::SUCCESS, $flash);
	}

	public function render()
	{
		$output = '';

		if( $this->_mensagens )
		{
			foreach( $this->_mensagens as $m )
			{
				$output .= '<div class="ui-notificacao ui-notificacao--'.$m['codigo'].'">';
					$output .= $m['texto'];
				$output .= '</div>';
			}
		}

		return $output;
	}

}
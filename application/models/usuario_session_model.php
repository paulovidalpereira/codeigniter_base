<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_session_model extends MY_Model {

    protected $_usuario;

    public function __construct()
    {
        parent::__construct();

        $this->load->helper('security');
    }

    public function isLogged($redirect = FALSE)
    {
        if( $this->session->userdata('id') && $this->session->userdata('token') )
        {
            return $this->checkUsuario();
        }

        if( $redirect )
        {
            redirect('autenticacao/entrar');
        }

        return FALSE;
    }

    public function checkUsuario()
    {
        $usuario = $this->usuario_m->load($this->session->userdata('id'));

        if( $usuario )
        {
            if( $this->session->userdata('token') == do_hash($usuario->getId().$usuario->getSenha()) )
            {   
                return TRUE;
            }
        }

        $this->logout();

        return FALSE;
    }

    public function setSession($usuario)
    {
        $this->session->set_userdata(array(
            'id' => $usuario->getId(),
            'nome' => $usuario->getNome(),
            'token' => do_hash($usuario->getId().$usuario->getSenha())
        ));
    }

    public function getId()
    {
        return $this->session->userdata('id');
    }

    public function getNome()
    {
        return $this->session->userdata('nome');
    }

    public function logout()
    {
        $this->session->sess_destroy();
    }

}
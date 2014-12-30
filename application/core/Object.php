<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class Object {

    public function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

}
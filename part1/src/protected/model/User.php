<?php
class User{
	function check($user, $pass){
	    foreach( $GLOBALS['user'] as $config_user => $config_pass){
            if($config_user == $user && $config_pass == $pass){
                return true;
            }
        }
        return false;
	}
}
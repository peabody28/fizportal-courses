<?php
require_once __DIR__ . "/../classes/Users_table.php";
require_once __DIR__."/../classes/User_session.php";
require_once __DIR__."/../classes/Validator.php";

class User
{
    public $id, $name, $password, $rights, $existence=false, $hash=null;

    public function get()
    {
        $users_table = new Users_table();
        $user = $users_table->read($this);
        if($user)
        {
            $this->name = $user->name;
            $this->password = $user->password;
            $this->rights = $user->rights;
            $this->hash = $user->hash;
            $this->existence = true;
        }

    }
    public function generate_code($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
}
<?php
require_once __DIR__."/../classes/Users_db.php";
require_once __DIR__."/../classes/User_session.php";
require_once __DIR__."/../classes/Validator.php";

class User
{
    public $id, $name, $password, $rights=null, $existence=false, $create_cookie=false, $hash=null;

    public function add(): array
    {
        $validator = new Validator();
        $validation_response = $validator->valid_user_data($this);

        if($validation_response["status"]=="ERROR")
            return $validation_response;
        else
        {
            // проверка на доступность имени
            $users_db = new Users_db();
            $this->existence = $users_db->check_existence_username($this);
            if($this->existence)
                return ["status"=>"ERROR", "error"=>"Это имя занято"];

            $response = $users_db->add($this);
            if($response["status"]=="OK")
                $this->remember();
            return $response;
        }

    }
    public function login(): array
    {
        if($this->name=="" or $this->password=="")
            return ["status"=>"ERROR", "error"=>"Заполни поля"];
        else
        {
            $users_db = new Users_db();
            $response = $users_db->search_user($this);
            if($response["status"]=="OK")
                $this->remember();
            return $response;
        }
    }
    public function get_user()
    {
        $users_db = new Users_db();
        $users_db->read($this);
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
    public function remember()
    {
        if($this->create_cookie and strlen($this->hash)==0)
        {
            $users_db = new Users_db();
            $this->hash = md5($this->generate_code());
            $users_db->update_hash($this);
        }
        $session = new User_session();
        $session->create($this);
    }
}
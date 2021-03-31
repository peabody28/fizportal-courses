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
            // добавление в базу
            $response = $users_db->add($this);
            if($response["status"]=="OK")
                $this->remember(); // создание куки
            return $response;
        }

    }
    public function login(): array
    {
        if($this->name=="" or $this->password=="")
            return ["status"=>"ERROR", "error"=>"Заполни поля"];
        else
        {
            // поиск в базе пользователей
            $users_db = new Users_db();
            $serching_user = $users_db->search_user($this);
            if($serching_user)
            {
                if($serching_user->password == md5(md5($this->password)))
                {
                    // дополняю обьект данными из БД
                    $this->id = $serching_user->id;
                    $this->rights = $serching_user->rights;
                    $this->hash = $serching_user->hash;
                    $this->existence = true;
                    $this->remember();
                    return ["status" => "OK"];
                }
                else
                    return ["status"=>"ERROR", "error"=>"Неверный пароль"];
            }
            else
                return ["status"=>"ERROR", "error"=>"Такое имя не зарегестрировано"];
        }
    }
    public function get()
    {
        $users_db = new Users_db();
        $users_db->read($this);
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
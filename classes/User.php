<?php
require_once __DIR__ . "/../classes/Users_table.php";
require_once __DIR__."/../classes/User_session.php";
require_once __DIR__."/../classes/Validator.php";

class User
{
    public $id, $name, $password, $rights, $existence=false, $create_cookie=false, $hash=null;

    public function add(): array
    {
        $validator = new Validator();
        $validation_response = $validator->valid_user_data($this);
        if($validation_response["status"]=="ERROR")
            return $validation_response;
        else
        {
            // проверка на доступность имени
            $isset = $this->check_existence();
            if($isset)
                return ["status"=>"ERROR", "error"=>"Это имя занято"];

            // добавление в базу
            $users_table = new Users_table();
            $this->existence = $users_table->create($this);
            if($this->existence) // проверка на успешность добавления в базу
            {
                $this->remember(); // создание куки
                return ["status"=>"OK"];
            }
            else
               return ["status"=>"ERROR", "error"=>"Не удалось создать пользователя"];
        }

    }
    public function login(): array
    {
        if($this->name=="" or $this->password=="")
            return ["status"=>"ERROR", "error"=>"Заполни поля"];
        else
        {
            // поиск в базе пользователей
            $isset = $this->check_existence();

            if($isset)
            {
                $searching_user = new User();
                $searching_user->id = $isset;
                $searching_user->get();

                if($searching_user->password == md5(md5($this->password)))
                {
                    // дополняю обьект данными из БД
                    $this->id = $searching_user->id;
                    $this->rights = $searching_user->rights;
                    $this->hash = $searching_user->hash;
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
    public function check_existence()
    {
        $users_table = new Users_table();
        return $users_table->check_existence_username($this);
    }
    public function remember()
    {
        if($this->create_cookie and strlen($this->hash)==0)
        {
            $users_db = new Users_table();
            $this->hash = md5($this->generate_code());
            $users_db->update($this, "hash");
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
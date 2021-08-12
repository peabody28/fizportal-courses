<?php
require_once __DIR__."/Validator.php";
require_once __DIR__."/Users_table.php";
require_once __DIR__."/User_session.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Users_themes_time_table.php";



class User
{
    public $id, $name, $email, $rights, $hash=null, $password;

    public function __construct($id=null)
    {
        if($id !== null)
        {
            $users_table = new Users_table();
            $tmp_user = $users_table->read($id);
            $this->id = (int)$tmp_user["id"];
            $this->name = $tmp_user["name"];
            $this->email = $tmp_user["email"];
            $this->password = $tmp_user["password"];
            $this->rights = $tmp_user["rights"];
            $this->hash = $tmp_user["hash"];
        }

    }

    public function login($data)
    {
        if($data["name_or_email"]=="" or $data["password"]=="")
            $response = ["status"=>"ERROR", "error"=>"Заполни поля"];
        else
        {
            // поиск в базе пользователей
            $users_table = new Users_table();
            $user_data = $users_table->check_existence($data["name_or_email"]); //данные найденного пользователя
            if($user_data)
            {
                if($user_data["password"] == md5(md5($data["password"])))
                {
                    // дополняю обьект данными из БД
                    $user = new User($user_data["id"]);

                    // создаю сессию
                    $session = new User_session();
                    $session->create_session($user);
                    if(isset($data["check"]))
                    {
                        if(strlen($user->hash)==0)
                        {
                            $user->hash = $session->generate_code();
                            $users_table->update($user, "hash");
                        }
                        $session->create_cookie($user);
                    }
                    $response = ["status" => "OK"];
                }
                else
                    $response = ["status"=>"ERROR", "error"=>"Неверный пароль"];
            }
            else
                $response = ["status"=>"ERROR", "error"=>"Не удалось вас найти"];
        }
        return $response;
    }

    public function signup($data)
    {
        // создание объекта пользователя
        $user = new User();
        $user->name = $data["name"];
        $user->email = $data["email"];
        $user->password = $data["password"];
        // валидация данных
        $validator = new Validator();
        $validation_response = $validator->valid_user_data($user);
        if($validation_response["status"]=="ERROR")
            $response = $validation_response;
        else
        {
            // проверка на доступность имени
            $users_table = new Users_table();
            $name_status = $users_table->check_existence_username($user->name); //id найденного пользователя
            $email_status = $users_table->check_existence_email($user->email); //id найденного пользователя
            if($name_status)
                $response = ["status"=>"ERROR", "error"=>"Это имя занято"];
            else if($email_status)
                $response = ["status"=>"ERROR", "error"=>"Данный email уже зарегестрирован"];
            else
            {
                // добавление в базу
                $response = $users_table->create($user);
                if($response) // проверка на успешность добавления в базу
                {
                    // создаю сессию
                    $session = new User_session();
                    $session->create_session($user);
                    if(isset($data["check"]))
                    {
                        $user->hash = $session->generate_code();
                        $users_table->update($user, "hash");
                        $session->create_cookie($user);
                    }
                    $response = ["status" => "OK"];
                }
                else
                    $response = ["status"=>"ERROR", "error"=>"Не удалось создать пользователя"];
            }
        }
        return $response;
    }
}
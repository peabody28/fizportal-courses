<?php
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
require_once __DIR__."/classes/User_session.php";
require_once __DIR__."/classes/Render.php";


$data = $_POST;

if (isset($data["submit"]))
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
                if($data["check"])
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
    echo json_encode($response);
}
else
{
    echo file_get_contents("templates/login.html");
}


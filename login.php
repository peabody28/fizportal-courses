<?php
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
require_once __DIR__."/classes/User_session.php";
require_once __DIR__."/classes/Render.php";


$data = $_POST;

if (isset($data["submit"]))
{
    $user = new User();
    $user->name = $data["name"];

    if($user->name=="" or $data["password"]=="")
        $response = ["status"=>"ERROR", "error"=>"Заполни поля"];
    else
    {
        // поиск в базе пользователей
        $users_table = new Users_table();
        $user_data = $users_table->check_existence_username($user->name); //данные найденного пользователя
        if($user_data)
        {
            if($user_data["password"] == md5(md5($data["password"])))
            {
                // дополняю обьект данными из БД
                $user->id = $user_data["id"];
                $user->password = $data["password"];
                $user->rights = $user_data["rights"];
                $user->hash = $user_data["hash"];

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
            $response = ["status"=>"ERROR", "error"=>"Такое имя не зарегестрировано"];
    }
    echo json_encode($response);
}
else
{
    $page = new Render();
    $page->temp = 'login.html';
    $page->argv = ['title'=>"login", 'nm'=>"ВХОД",
        'btn_text'=>"Войти", 'a_href'=>"/signup", 'a_text'=>"создать акк", "js"=>"/js/login.js"]  ;
    echo $page->render_temp();
}


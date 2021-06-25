<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
require_once __DIR__."/classes/User_session.php";
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
        $user->id = $users_table->check_existence_user($user->name); //id найденного пользователя
        if($user->id)
        {
            $searching_user = $users_table->read($user->id); // беру данные пользователя для сверки
            if($searching_user["password"] == md5(md5($data["password"])))
            {
                // дополняю обьект данными из БД
                $user->password = $data["password"];
                $user->rights = $searching_user["rights"];
                $user->hash = $searching_user["hash"];

                // создаю сессию
                $session = new User_session();
                $session->create_session($user);
                if($data["check"])
                {
                    if(strlen($user->hash)==0)
                    {
                        $user->hash = md5($session->generate_code());
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
        'btn_text'=>"Войти", 'a_href'=>"/signup.php", 'a_text'=>"создать акк", "js"=>"/js/login.js"]  ;
    echo $page->render_temp();
}


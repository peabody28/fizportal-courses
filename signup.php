<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
require_once __DIR__."/classes/User_session.php";
require_once __DIR__."/classes/Validator.php";
$data = $_POST;

if (isset($data["submit"]))
{
    // создание объекта пользователя
    $user = new User();
    $user->name = $data["name"];
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
        $isset = $users_table->check_existence_user($user); //id найденного пользователя
        if($isset)
           $response = ["status"=>"ERROR", "error"=>"Это имя занято"];
        // добавление в базу
        $response = $users_table->create($user);
        if($response) // проверка на успешность добавления в базу
        {
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
            $response = ["status"=>"ERROR", "error"=>"Не удалось создать пользователя"];
    }
    echo json_encode($response);

}
else
{
    $page = new Render();
    $page->temp = 'login.html';
    $page->argv = ['title'=>"signup", 'nm'=>"РЕГИСТРАЦИЯ",
        'btn_text'=>"Создать", 'a_href'=>"/login.php", 'a_text'=>"Войти", "js"=>"/js/signup.js"] ;
    echo $page->render_temp();
}

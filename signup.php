<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Validator.php";
require_once __DIR__."/classes/Users_table.php";
require_once __DIR__."/classes/User_session.php";


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
        $user_data = $users_table->check_existence_username($user->name); //id найденного пользователя
        if($user_data)
           $response = ["status"=>"ERROR", "error"=>"Это имя занято"];
        else
        {
            // добавление в базу
            $response = $users_table->create($user);
            if($response) // проверка на успешность добавления в базу
            {
                // создаю сессию
                $session = new User_session();
                $session->create_session($user);
                if($data["check"])
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

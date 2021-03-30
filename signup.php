<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/User.php";

$data = $_POST;

if (isset($data["submit"]))
{
    // создание объекта пользователя
    $user = new User();
    $user->name = $data["name"];
    $user->password = $data["password"];
    $user->create_cookie = ($data["check"]=="on")?1:0;
    $response = $user->add();
    echo json_encode($response);

}
else
{
    $page = new Render();
    $page->temp = 'login.html';
    $page->argv = ['title'=>"signup", 'nm'=>"РЕГИСТРАЦИЯ",
        'btn_text'=>"Создать", 'a_href'=>"/login.php", 'a_text'=>"Войти", "js"=>"/js/signup.js"] ;
    echo $page->render_page();
}

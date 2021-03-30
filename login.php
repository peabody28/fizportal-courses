<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/User.php";
$data = $_POST;

if (isset($data["submit"]))
{
    $user = new User();
    $user->name = $data["name"];
    $user->password = $data["password"];
    $user->create_cookie = $data["check"];
    $response = $user->login();
    echo json_encode($response);
}
else
{
    $page = new Render();
    $page->temp = 'login.html';
    $page->argv = ['title'=>"login", 'nm'=>"ВХОД",
        'btn_text'=>"Войти", 'a_href'=>"/signup.php", 'a_text'=>"создать акк", "js"=>"/js/login.js"]  ;
    echo $page->render_page();
}


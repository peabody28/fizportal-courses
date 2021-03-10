<?php
require_once "vendor/autoload.php";
require_once "db.php";

$data = $_POST;

if (isset($data["submit"]))
{
    $user = R::dispense("users");
    $user->name =$data["name"];
    $user->password =$data["password"];
    $id = R::store($user);
    if($id)
        echo json_encode(["status"=>"OK"]);
    else
        echo json_encode(["status"=>"ERROR", "error"=>"not work("]);
}
else
{
    $loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
    $twig = new Twig\Environment($loader);

    echo $twig->render('login.html',
        ['title'=>"signup", 'nm'=>"РЕГИСТРАЦИЯ", 'code'=>"signup",
            'btn_text'=>"Создать аккаунт", 'a_href'=>"/login.php", 'a_text'=>"Логин", "js"=>"/js/SignUp.js"] );

}

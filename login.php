<?php
session_start();
require_once "vendor/autoload.php";
require_once "db.php";

$data = $_POST;

if (isset($data["submit"]))
{
    if(trim($data["name"])=="" or trim($data["password"])=="")
        echo json_encode(["status"=>"ERROR", "error"=>"Заполни поля"]);
    else
    {
        $user = R::findOne("users", "WHERE name = ?", [$data["name"]]);
        if($user)
        {
            if( $user->password == md5(md5( $data["password"] )) )
            {
                $_SESSION["name"] = $data["name"];
                echo json_encode(["status" => "OK"]);
            }
            else
                echo json_encode(["status"=>"ERROR", "error"=>"Ты плохо помнишь пароль"]);
        }
        else
            echo json_encode(["status"=>"ERROR", "error"=>"Нету тебя в списочке моем"]);
    }
}
else
{
    $loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
    $twig = new Twig\Environment($loader);

    echo $twig->render('login.html',
        ['title'=>"login", 'nm'=>"ВХОД",
            'btn_text'=>"Войти", 'a_href'=>"/signup.php", 'a_text'=>"создать акк", "js"=>"/js/login.js"] );

}


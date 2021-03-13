<?php
session_start();
require_once "vendor/autoload.php";
require_once "db.php";

$data = $_POST;

if (isset($data["submit"]))
{

    if(trim($data["name"])=="" or trim($data["password"])=="")
        echo json_encode(["status"=>"ERROR", "error"=>"Заполни поля"]);
    //проверка на пробелы и запрещенные символы в имени
    else
    {
        $user = R::dispense("users");
        $user->name =trim(htmlspecialchars($data["name"]));
        $user->password = md5(md5($data["password"]));
        $id = R::store($user);
        if($id)
        {
            $_SESSION["name"] = $data["name"];
            echo json_encode(["status"=>"OK"]);
        }
        else
            echo json_encode(["status"=>"ERROR", "error"=>"not work("]);
    }

}
else
{
    $loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
    $twig = new Twig\Environment($loader);

    echo $twig->render('login.html',
        ['title'=>"signup", 'nm'=>"РЕГИСТРАЦИЯ",
            'btn_text'=>"Создать", 'a_href'=>"/login.php", 'a_text'=>"Войти", "js"=>"/js/signup.js"] );

}

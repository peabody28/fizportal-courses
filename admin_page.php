<?php
require_once "db.php";
require_once "auth.php";
require_once "auth_root.php";
require_once __DIR__."/vendor/autoload.php";


$rem = "";
$content =
    "
    <div class='d-flex justify-content-center w-100 pt-5'>ДОБАВИТЬ КУРС</div>
    <div class='row m-0 w-100 justify-content-center'>
     
        <form class='course-actions' method='POST'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='code' value='add'>
            <input type='text' name='cours_name' placeholder='Название курса'><br><br>
            <input type='text' name='title' placeholder='Описание'><br><br>
            <button type='submit' class='btn btn-md btn-primary'>Создать</button>
        </form>
    </div>
    <div id='resp'></div>
    <div class='d-flex h2 justify-content-center w-100 pt-5'>Существующие курсы</div>";

R::selectDatabase("courses_list");
$courses_list = R::findAll("courses");
R::selectDatabase("default");

foreach ($courses_list as $cours)
    $content.="
    <div class=' pl-5 row w-100 p-2 m-0'>
        <div class='col-2 d-flex align-items-center justify-content-center'>
            <a class='col' href='http://127.0.0.1/change_cours.php?id=$cours->id'>$cours->name</a>    
        </div> 
        <form class='col course-actions' method='POST'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='code' value='rm'>
            <input type='hidden' name='id' value='$cours->id'>
            <button type='submit' class='btn btn-md btn-primary'>Удалить</button>
        </form>
    </div>";
$loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new Twig\Environment($loader);

$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title' => "main",
        'css' => "/css/main.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "disabled_$file" => "",
        "js" => "/js/admin_page.js"]);


<?php
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

//беру данные из базы
//кладу в шаблон
//вывожу шаблоны

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);
$content = "";
if($_SESSION["rights"]=="admin")
    $content.= "<div class='row w-100 p-2 m-0 justify-content-center'><a class='btn btn-primary' href='http://127.0.0.1/admin_page.php'>Админка</a></div>";
$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title'=>"acc",
        'css'=>"/css/acc.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "disabled_$file"=>"disabled",
        "js"=>"/js/acc.js"
    ] );

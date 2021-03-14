<?php
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

//беру данные из базы
//кладу в шаблон
//вывожу шаблоны

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title'=>"acc",
        'css'=>"/css/acc.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>"кабинет v.01",
        "disabled_$file"=>"disabled",
        "js"=>"/js/acc.js"
    ] );

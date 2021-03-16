<?php
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title'=>"main",
        'css'=>"/css/main.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>"Главная страница v0.3",
        "disabled_$file"=>"disabled",
        "js"=>"/js/main.js"] );

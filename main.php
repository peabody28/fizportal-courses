<?php
require_once __DIR__."/vendor/autoload.php";
session_start();

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

echo $twig->render('main.html',
    ['title'=>"main", 'css'=>"/css/main2.css", "content"=>"<div id='trs'><div id='tr-red'></div><div id='tr-black'></div></div><div id='text'>Hello $_SESSION[name]</div>", "js"=>"/js/Main.js"] );

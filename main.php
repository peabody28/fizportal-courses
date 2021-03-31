<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Render.php";
session_start();

$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv =
    ['title'=>"main",
    'css'=>"/css/main.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>"Главная страница v0.3",
    "disabled_$file"=>"disabled",
    "js"=>"/js/main.js"] ;

echo $page->render_temp();


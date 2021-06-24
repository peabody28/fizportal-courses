<?php
require_once __DIR__."/classes/Render.php";
session_start();

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"task $_GET[id]",
    'css'=>"/css/task.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$_GET["id"],
    "js"=>"/js/task.js"] ;

echo $page->render_temp();
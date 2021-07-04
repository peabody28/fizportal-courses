<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Render.php";
session_start();


$content = "";

//if($_SESSION["rights"]=="admin")
   // $content.= "<div class='row w-100 p-2 m-0 justify-content-start'><a class='btn adm_btn' href='/admin_page'>Админка</a></div>";

$content.= "<div class='row w-100 p-2 m-0 justify-content-start'><a id='exit' class='btn btn-md' href='/exit'>Выйти</a></div>";
$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"acc",
    'css'=>"/css/acc.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "disabled_$file"=>"disabled",
    "js"=>"/js/acc.js"
] ;

echo $page->render_temp();




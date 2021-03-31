<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Render.php";


$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv =
    ['title'=>"faq",
    'css'=>"/css/faq.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>"this is FAQ page",
    "disabled_$file"=>"disabled",
    "js"=>""];
echo $page->render_temp();

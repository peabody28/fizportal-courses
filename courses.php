<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";
session_start();

$data = $_GET;

$cours = new Course();
$content = $cours->get_courses();

// чтоб сделать кнопку неактивной
$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"courses_list",
    'css'=>"/css/courses.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "disabled_$file"=>"disabled",
    "js"=>"/js/courses.js"] ;

echo $page->render_page();
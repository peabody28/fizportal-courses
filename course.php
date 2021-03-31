<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Theme.php";
session_start();

$data = $_GET;

$course = new Course();
$course->id = $data["id"];
$course->get();
if ($course->existence)
{
    $themes = new Theme();
    $content = $themes->get_html($course->themes);
}
else
    $content = "Такого курса нет";


$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"$course->name",
    'css'=>"/css/cours.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "js"=>"/js/cours.js"] ;

echo $page->render_temp();




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";
session_start();

$data = $_GET;

$courses_table = new Courses_table();
$course = $courses_table->read($data["id"]);

if ($course["id"])
{
    $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Темы</h2></div>";
    //беру темы курса
    $themes_table = new Themes_table();
    $themes_list = $themes_table->get_themes_course($course["id"]);
    $render = new Render();
    $content .= $render->render_theme($themes_list);
}
else
    header("Location: /courses.php");


$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"$course[name]",
    'css'=>"/css/cours.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "js"=>"/js/cours.js"] ;

echo $page->render_temp();




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";
session_start();

$data = $_GET;

$course = new Course();
$course->id = $data["id"];
$courses_table = new Courses_table();
$tmp_course = $courses_table->read($course);
if ($tmp_course)
{
    //беру темы курса
    $themes_table = new Themes_table();
    $themes_list = $themes_table->get_themes_course($course);
    $render = new Render();
    $content = $render->render_theme($themes_list);
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




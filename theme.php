<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$theme = new Theme();
$theme->id = $data["id"];
$themes_table = new Themes_table();
$tmp_theme = $themes_table->read($theme);
if ($tmp_theme)
{
    //беру темы курса
    $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Задачи</h2></div>";
    $tasks_table = new Tasks_table();
    $tasks_list = $tasks_table->get_tasks_theme($theme);
    $render = new Render();
    $content .= $render->render_task($tasks_list);
}
else
    $content = "Такого курса нет";


$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"$theme->title",
    'css'=>"/css/cours.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "js"=>"/js/cours.js"] ;

echo $page->render_temp();

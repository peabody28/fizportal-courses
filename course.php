<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$courses_table = new Courses_table();
$course = $courses_table->read($data["id"]);

if ($course["id"])
{
    // проверяю, есть ли курс у пользователя
    $users_courses_table = new Users_courses_table();
    $users_courses_list = $users_courses_table->read($_SESSION["id"]);
    if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$course["id"]], $users_courses_list))
    {
        $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Темы</h2></div>";
        //беру темы курса
        $themes_table = new Themes_table();
        $themes_list = $themes_table->get_courses_themes($course["id"]);
        $render = new Render();
        $content .= $render->render_theme($themes_list);
    }
    else
        $content = "Вы не купили этот курс";
}
else
    header("Location: /courses");

$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"$course[name]",
    'css'=>"/css/course.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "js"=>"/js/course.js"] ;

echo $page->render_temp();




<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/classes/Course_block.php";
require_once __DIR__ . "/classes/Courses_table.php";
require_once __DIR__ . "/classes/Users_courses_table.php";
require_once __DIR__ . "/classes/Render.php";
session_start();


$data = $_POST;

$users_courses_table = new Users_courses_table();
$users_courses_rows = $users_courses_table->read($_SESSION["id"]);

// рендеринг
$content = "<div class='row container-fluid justify-content-center m-0 mt-5 p-0'><h1>Мои курсы</h1></div>";
$course_block = new Course_block();
$courses_table = new Courses_table();
foreach ($users_courses_rows as $row)
{
    $tmp_course = $courses_table->read($row["course_id"]);
    $course_block->argv = ["title"=>$tmp_course["title"], "text"=>$tmp_course["text"], "id"=>$tmp_course["id"], "img_url"=>$tmp_course["img_url"]];
    $content .= $course_block->render();
}

// чтоб сделать кнопку неактивной
$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "my_courses",
    'css' => "/css/my_courses.css",
    "name" => "<h2>$_SESSION[name]</h2>",
    "content" => $content,
    "disabled_$file" => "disabled",
    "js" => "/js/my_courses.js"];

echo $page->render_temp();



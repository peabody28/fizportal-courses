<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/classes/Course.php";
require_once __DIR__ . "/classes/Course_block.php";
require_once __DIR__ . "/classes/Courses_table.php";
require_once __DIR__ . "/classes/Users_courses_table.php";
require_once __DIR__ . "/classes/Render.php";
session_start();

$data = $_POST;

$user_id = $_SESSION["id"];
$users_courses_table = new Users_courses_table();
$users_courses_list_ids = $users_courses_table->read($user_id);

$users_courses_list = array();

foreach ($users_courses_list_ids as $course_id)
{
    $course = new Course();
    $course->id = $course_id;
    array_push($users_courses_list, $course->get());
}

//рендеринг
$content = "";
$block = new Course_block();
foreach ($users_courses_list as $course)
{
    $block->argv = ["title"=>$course->title, "text"=>$course->text, "id"=>$course->id];
    $content .= $block->render();
}

// чтоб сделать кнопку неактивной
$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "courses_list",
    'css' => "/css/courses.css",
    "name" => "<h2>$_SESSION[name]</h2>",
    "content" => $content,
    "disabled_$file" => "disabled",
    "js" => "/js/courses.js"];

echo $page->render_temp();



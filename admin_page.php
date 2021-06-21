<?php
require_once __DIR__."/db.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Theme.php";


// существующие курсы + добавление курса
$content = "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/create_course.php'>Cоздать курс</a> </div><br><br>";

//Отображение существующих курсов специально для админа
$courses = new Courses_table();
$courses_list = $courses->get_courses_list();
foreach ($courses_list as $course)
{
    $block = new Render();
    $block->temp = "course_block_adm.html";
    $block->argv = ["title"=>$course->title, "text"=>$course->text, "id"=>$course->id];
    $content .= $block->render_temp();
}

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "adminpage",
    'css' => "/css/admin_page.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => $content,
    "js" => "/js/admin_page.js"];
echo $page->render_temp();


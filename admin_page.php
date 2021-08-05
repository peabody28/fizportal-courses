<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Course_block_adm.php";
session_start();


// существующие курсы + добавление курса
$content = "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/create_course'>Cоздать курс</a> </div><br><br>";

//Отображение существующих курсов специально для админа
$courses_table = new Courses_table();
$courses_list = $courses_table->get_courses_list();

$course_block_adm = new Course_block_adm();
foreach ($courses_list as $course)
{
    $course_block_adm->argv = ["title"=>$course["title"], "id"=>$course["id"]];
    $content .= $course_block_adm->render();
}

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "adminpage",
    'css' => "/css/admin_page.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => $content,
    "js" => "/js/admin_page.js"];
echo $page->render_temp();


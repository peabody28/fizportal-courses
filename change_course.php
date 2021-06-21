<?php
session_start();
require_once __DIR__."/auth_root.php";
require_once __DIR__."/auth.php";
require_once __DIR__."/db.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";


$course = new Course();
$course->id = $_GET["id"];
$courses_table = new Courses_table();
$tmp_course = $courses_table->read($course);
$course->title = $tmp_course->title;
$course->text = $tmp_course->text;
$course->complexity = $tmp_course->complexity;
$course->price = $tmp_course->price;

// TODO: Отобразить поля изменения курса (title, text, price, complexity)

//получаю темы
$content = "";
$themes_table = new Themes_table();
$themes_list = $themes_table->get_themes_course($course);

$theme_block = new Render();
$theme_block->temp = "theme_block_adm.html";
foreach ($themes_list as $theme) {

    $theme_block->argv = ["title"=>$theme["title"], "id"=>$theme["id"]];
    $content .= $theme_block->render_temp();
}
// поле создания темы
$content.= "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/add_theme.php?course_id=$course->id'>Добавить тему</a> </div><br><br>";
$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "main",
    'css' => "/css/admin_page.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => $content,
    "js" => "/js/admin_page.js"];
echo $page->render_temp();

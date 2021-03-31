<?php
require_once __DIR__."/db.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Theme.php";

if($_GET["code"]=="create_course")
{
    $content = file_get_contents(__DIR__."/templates/create_course_form.html");
}
else if($_GET["code"]=="change_course")
{
    // существующие темы + добавление темы

    // получаю данные изменяемого курса
    $course = new Course();
    $course->id = $_GET["id"];
    $course->get();
    //отображение существующих в курсе тем
    $content = "";
    $theme_block_adm = new Render();
    $theme_block_adm->temp = "theme_block_adm.html";
    foreach ($course->themes as $item)
    {
        $theme = new Theme();
        $theme->id = $item;
        $theme->get();
        $theme_block_adm->argv = ["id"=>$theme->id, "name"=>$theme->name];
        $content .= $theme_block_adm->render_temp();
    }
    // форма добавление новой темы
    $add_theme_form = new Render();
    $add_theme_form->temp = "add_theme_form.html";
    $add_theme_form->argv = ["course_id"=>$course->id];
    $content .= $add_theme_form->render_temp();
}
else if($_GET["code"]=="change_theme")
{
    // существующие задачи + добавление новых

    // получаю данные изменяемой темы
    $content = $_GET["id"];
}
else
{
    // существующие курсы + добавление курса
    $content = "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/admin_page.php?code=create_course'>Cоздать курс</a> </div><br><br>";
    //Отображение существующих курсов специально для админа
    $courses = new Course();
    $courses_list = $courses->get_courses();
    foreach ($courses_list as $course)
    {
        $block = new Render();
        $block->temp = "course_block_adm.html";
        $block->argv = ["name"=>$course->name, "id"=>$course->id];
        $content .= $block->render_temp();
    }
}

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "main",
    'css' => "/css/admin_page.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => $content,
    "js" => "/js/admin_page.js"];
echo $page->render_temp();


<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Users_progress_theme_table.php";
require_once __DIR__."/classes/Themes_points_limit_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$courses_table = new Courses_table();
$course = $courses_table->read($data["id"]);

if ($course["id"])
{
    // проверяю, есть ли курс у пользователя
    $manager = new Manager();
    $status = $manager->check_course($_SESSION["id"], $course["id"]);
    if($status || $_SESSION["rights"]=="admin")
    {
        $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Темы</h2></div>";
        if($_SESSION["rights"]=="admin")
            $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_theme?course_id=$course[id]'>Добавить тему</a></div><br><br>";
        //беру темы курса
        $themes_table = new Themes_table();
        $themes_list = $themes_table->get_courses_themes($course["id"]);

        // отображаю
        for ($theme_id=0; $theme_id<count($themes_list); $theme_id++) {
            $theme = $themes_list[$theme_id];

            $professor = new Professor();
            $theme_status = $professor->theme_status($theme);

            if ($theme_status=="solved")
                $class = "green_theme";
            else if ($theme_status=="open")
                $class = "open_theme";
            else
                $class = "close_theme";
            if($_SESSION["rights"]=="admin")
                $class = "open_theme";


            $themes_points_limit_table = new Themes_points_limit_table();
            $resp = $themes_points_limit_table->read($theme["id"]);
            $theme["points_limit"] = $resp["points_limit"]?:10; // если лимит не установен, принимаем его за 10 баллов

            $users_progress_theme_table = new Users_progress_theme_table();
            $progress = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$theme["id"]]);

            $progress = $progress?$progress["progress"]:"0";
            $render = new Render();
            $theme_block = $render->render_theme($theme, $class, $progress, ($_SESSION["rights"]=="admin"));

            $content .= $theme_block["block"];
        }

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




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$course = new Course($data["id"]);

if ($course->id)
{
    // проверяю, есть ли курс у пользователя
    $user = new User($_SESSION["id"]);
    $manager = new Manager();
    $status = $manager->check_course($user->id, $course->id);

    if($status || $user->rights == "admin")
    {
        $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Темы</h2></div>";
        if($user->rights == "admin")
            $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_theme?course_id=$course->id'>Добавить тему</a></div><br><br>";

        // беру id тем курса
        $themes_ids = $course->get_themes_ids();
        // отображаю
        foreach ($themes_ids as $id) {
            $theme = new Theme($id);

            $professor = new Professor();
            $theme_status = $professor->theme_status($user, $theme);

            if ($theme_status=="solved")
                $class = "green_theme";
            else if ($theme_status=="open")
                $class = "open_theme";
            else
                $class = "close_theme";
            if($user->rights == "admin")
                $class = "open_theme";


            $theme->get_points_limit();

            $progress = $professor->get_progress_theme($user, $theme);
            $progress = $progress?:"0";

            $render = new Render();
            $theme_block = $render->render_theme($theme, $class, $progress, ($user->rights == "admin"));

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
$page->argv = ['title'=>"$course->title",
    'css'=>"/css/course.css",
    "name"=>"<h2>$user->name</h2>",
    "content"=>$content,
    "js"=>"/js/course.js",
    "mathjax"=>file_get_contents("templates/mathjax.html")] ;

echo $page->render_temp();




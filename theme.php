<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$themes_table = new Themes_table();
$tmp_theme = $themes_table->read($data["id"]);
if ($tmp_theme)
{
    $users_courses_table = new Users_courses_table();
    $users_courses = $users_courses_table->read($_SESSION["id"]);
    if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$tmp_theme["course_id"]], $users_courses))
    {
        $tasks_table = new Tasks_table();
        //беру задачи темы
        $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>";
        $tasks_list = $tasks_table->get_tasks_theme($tmp_theme["id"]);

        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($_SESSION["id"]);

        foreach ($tasks_list as $task) {
            if( in_array(["user_id"=>$_SESSION["id"], "task_id"=>$task["id"]], $users_tasks) )
                $button = "<button class='btn' id='$task[id]'></button>";
            else
                $button = "<button class='btn close_btn' id='$task[id]'></button>";
            $content.="<form class='get_task mr-1' method='POST'>
        <input type='hidden' name='task_id' value='$task[id]'>
        <input type='hidden' name='submit' value='true'>
        <input type='hidden' name='code' value='get_task_data'>
        $button
        </form>  ";
        }

        $content .= "</div><br><br>"."<div id='task'>$tmp_theme[text]</div><br><br>"."<div class='h2 d-flex justify-content-center' id='message'></div>";
    }
    else
        $content = "Вы не купили этот курс";

}
else
    header("Location: /my_courses.php");


$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>strip_tags($tmp_theme["title"]),
    'css'=>"/css/theme.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "js"=>"/js/theme.js"] ;

echo $page->render_temp();

<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Users_progress_theme_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;


$themes_table = new Themes_table();
$tmp_theme = $themes_table->read($data["id"]);

if ($tmp_theme)
{
    // проверка покупки курса
    $manager = new Manager();
    $resp = $manager->check_course($_SESSION["id"], $tmp_theme["course_id"]);

    if ($resp["status"] || $_SESSION["rights"]=="admin") {

        // проверка доступа к теме
        $professor = new Professor();
        $theme_status = $professor->theme_status($tmp_theme);
        if($theme_status == "open" || $theme_status == "solved" || $_SESSION["rights"]=="admin")
        {
            // прошло ли время блокировки темы?
            $status = $professor->check_time(["user_id"=>$_SESSION["id"], "theme_id"=>$tmp_theme["id"]]);
            if($status !== false) // true or "update"
            {
                //беру задачи темы
                $tasks_table = new Tasks_table();
                $tasks_list = $tasks_table->get_tasks_theme($tmp_theme["id"]);
                // сделанные пользователем задачи
                $users_tasks_table = new Users_tasks_table();
                $users_tasks = $users_tasks_table->get_users_tasks($_SESSION["id"]);
                // прогресс
                $users_progress_theme_table = new Users_progress_theme_table();
                $users_progress = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$tmp_theme["id"]]);
                // супертест
                $supertests_table = new Supertests_table();
                $tmp_sptest = $supertests_table->read_by_theme($tmp_theme["id"]);
                // рендер блоков задач и супертеста
                $render = new Render();
                $response = $render->render_tasks_theme($tmp_theme, $tasks_list, $users_tasks, $users_progress, $tmp_sptest);
                $content = $response["content"];

                if(count($tasks_list))
                {
                    if(isset($_GET["text"]))
                        $content .="<div id='task'><div class='row m-0 p-0 justify-content-center h2'>Описание темы</div><br><div class='row m-0 p-0 justify-content-center h2'>$tmp_theme[text]</div></div>";
                    else
                    {
                        // рендер первой задачи
                        $this_task = $tasks_list[0];
                        $content .="<div id='task'>";
                        $content .= $render->render_task($this_task);
                        if ($_SESSION["rights"] == "admin")
                        {
                            $content .= "<div class='row justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$this_task[id]'>Изменить задачу</a></div><br><br>";
                            $content .= " <div class='row d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($this_task[id]);return false;'>Удалить эту задачу</button>
                                           </div><br><br>";
                        }
                        // материалы для задачи
                        $content .= "<div class='h2 d-flex justify-content-center' id='message'></div>";
                        $content .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$this_task[id]'>Материалы для задачи</a></div>";
                        $content .= "</div><br>";
                    }
                }
                else
                    $content .="<div id='task'><div class='row m-0 p-0 justify-content-center h2'>Описание темы</div><br><div class='row m-0 p-0 justify-content-center h2'>$tmp_theme[text]</div></div>";
            }
            else
                $content = "<h2>Время решения темы истекло, возвращайтесь позже</h2>";
        }
        else
            $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>Вы пока не можете решать эту тему</div>";
    }
    else
        $content = $resp["error"];
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

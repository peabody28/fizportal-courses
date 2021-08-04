<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Themes_limits_table.php";
require_once __DIR__."/classes/Themes_points_limit_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Users_mistakes_table.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Users_progress_theme_table.php";
require_once __DIR__."/classes/Tasks_block_constructor.php";
require_once __DIR__."/classes/Render.php";
session_start();



if(isset($_POST["submit"]) && $_POST["code"] != "back_to_theme")
{
    $data = $_POST;
    $themes_points_limit_table = new Themes_points_limit_table();
    $resp = null;
    if($data["code"]=="change_limit_of_points")
    {
        if ($data["limit_of_points"])
        {
            $themes_points_limit_table->update(["theme_id"=>$data["id"], "points_limit"=>$data["limit_of_points"]], "points_limit");
            $resp = ["status"=>"OK"];
        }
        else
            $resp = ["status"=>"error"];
    }
    echo json_encode($resp);
}
else
{
    if(isset($_POST["submit"]) && $_POST["code"] == "back_to_theme")
    {
        $data=$_POST;
        $this_task_id=$data["task_id"];
    }
    else
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
                $response = $professor->check_time(["user_id"=>$_SESSION["id"], "theme_id"=>$tmp_theme["id"]]);

                if($response["status"] !== false) // true or "update"
                {
                    //беру задачи темы
                    $tasks_table = new Tasks_table();
                    $tasks_list = $tasks_table->get_tasks_theme($tmp_theme["id"]);
                    // сделанные пользователем задачи
                    $users_tasks_table = new Users_tasks_table();
                    $users_tasks = $users_tasks_table->get_users_tasks($_SESSION["id"]);
                    //РО
                    $users_mistakes_table = new Users_mistakes_table();
                    $users_mistakes = $users_mistakes_table->read($_SESSION["id"]);
                    // лимит задач темы
                    $themes_points_limit_table = new Themes_points_limit_table();
                    $resp = $themes_points_limit_table->read($tmp_theme["id"]);
                    $tmp_theme["points_limit"] = $resp["points_limit"]?:10;  // если лимит не установен, принимаем его за 10 баллов
                    // прогресс
                    $users_progress_theme_table = new Users_progress_theme_table();
                    $users_progress = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$tmp_theme["id"]]);
                    // супертест
                    $supertests_table = new Supertests_table();
                    $tmp_sptest = $supertests_table->read_by_theme($tmp_theme["id"]);
                    // рендер блоков задач и супертеста
                    $render = new Render();
                    $tasks_blocks = $render->render_tasks_theme($tmp_theme, $tasks_list, $users_tasks, $users_mistakes, $users_progress, $tmp_sptest);
                    $content = $tasks_blocks["content"];
                    // время
                    if(isset($response["sec"]))
                    {
                        $class = ($response["status"]===true)?"in_process":"lock";
                        $hours_null = "";
                        $min_null = "";
                        $sec_null = "";
                        if((int)($response["hours"]/10)==0)
                            $hours_null = "0";
                        if((int)($response["min"]/10)==0)
                            $min_null = "0";
                        if((int)($response["sec"]/10)==0)
                            $sec_null = "0";
                        $content .= "<div id='clock' class='row m-0 p-0 pr-md-5 mt-4 h2 d-flex justify-content-end $class'><div id='hours'>$hours_null$response[hours]</div>:<div id='min'>$min_null$response[min]</div>:<div id='sec'>$sec_null$response[sec]</div></div>";
                    }

                    $content .="<div id='task' class='p-0 m-0 mt-5 d-flex justify-content-center align-items-center row container-fluid'>
                                    <div id='tt' class='p-4 pt-5 m-0 ml-md-5 mr-md-5 row container-fluid d-flex justify-content-center'>";

                    if(count($tasks_list))
                    {
                        // рендер первой задачи
                        if(isset($this_task_id))
                        {
                            if($this_task_id=="supertest")
                                $content .= "<script type='text/javascript'>$(document).ready(function() { $('.supertest').submit(); });</script>";
                            else
                                $content .= "<script type='text/javascript'>$(document).ready(function() { $('#$this_task_id').parent().submit(); });</script>";
                        }
                        else
                        {
                            $this_task = $tasks_list[$tasks_blocks["first_id"]];
                            $content .= "<script type='text/javascript'>$(document).ready(function() { $('#$this_task[id]').parent().submit(); });</script>";
                        }

                    }
                    else
                        $content .= "<script type='text/javascript'>$(document).ready(function() { $('#get_text_theme').click(); });</script>";

                    $content .= "</div>"; // закрыл #tt

                    // кнопка "назад к темам" и "Обнулить прогресс темы"
                    $content .= "<div class='row col-12 m-0 p-0 pl-md-5 pr-md-5 mt-3 d-flex justify-content-between'>
                                <a class='btn col-12 col-md-3' id='back_to_themes_btn' href='/course?id=$tmp_theme[course_id]'>Назад к темам</a>
                                <button id='reset_theme' theme_id='$tmp_theme[id]' class='btn col-12 col-md-4 mt-3 mt-md-0'>Обнулить прогресс темы</button>
                             </div>";
                    $content .= "</div>"; // зкарыл #task
                }
                else
                {
                    $content = "<h2>Время решения темы истекло, возвращайтесь через</h2>";

                    $hours_null = "";
                    $min_null = "";
                    $sec_null = "";
                    if((int)($response["hours"]/10)==0)
                        $hours_null = "0";
                    if((int)($response["min"]/10)==0)
                        $min_null = "0";
                    if((int)($response["sec"]/10)==0)
                        $sec_null = "0";
                    $content .= "<br><div class='row m-0 p-0 h2 blocked'><div id='hours'>$hours_null$response[hours]</div>:<div id='min'>$min_null$response[min]</div>:<div id='sec'>$sec_null$response[sec]</div></div>";
                }

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
        "js"=>"/js/theme.js",
        "mathjax"=>file_get_contents("templates/mathjax.html")] ;

    echo $page->render_temp();

}

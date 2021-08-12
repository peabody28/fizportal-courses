<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Supertest.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Timer.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Themes_points_limit_table.php";
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
    else if($data["code"]=="reset_theme")
    {
        $user = new User($_SESSION["id"]);
        $theme = new Theme($data["id"]);

        $professor = new Professor();
        $resp = $professor->reset_theme($user, $theme);
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

    $user = new User($_SESSION["id"]);
    $theme = new Theme($data["id"]);

    if ($theme->id)
    {
        // проверка покупки курса
        $manager = new Manager();
        $resp = $manager->check_access_to_course($user, $theme->course_id);

        if ($resp["status"])
        {
            // проверка доступа к теме
            $professor = new Professor();
            $resp = $professor->theme_status($user, $theme);
            $theme_status = $resp["status"];

            if($theme_status == "open" || $theme_status == "solved" || $user->rights=="admin")
            {
                // прошло ли время блокировки темы?
                $response = $professor->check_time($user, $theme);

                if($response["status"] !== false) // true or "update"
                {
                    // отображение квадратов задач
                    // TODO написать тест для функции get_tasks_blocks()
                    $resp2 = $theme->get_tasks_blocks($user);
                    $content = $resp2["block"];
                    // отображаю время
                    if(isset($response["sec"]))
                    {
                        $timer = new Timer();
                        $class = ($response["status"]===true)?"in_process":"lock";
                        $time_str = $timer->seconds_normalizations($response["sec"]);
                        $content .= "<div id='clock' class='row m-0 p-0 pr-md-5 mt-4 h2 d-flex justify-content-end $class'>$time_str</div>";
                    }

                    $content .="<div id='task' class='p-0 m-0 mt-5 d-flex justify-content-center align-items-center row container-fluid'>
                                    <div id='tt' class='p-4 pt-5 m-0 ml-md-5 mr-md-5 row container-fluid d-flex justify-content-center'>";

                    if($resp2["count_tasks"])
                    {
                        // рендер первой задачи
                        if(isset($this_task_id))
                        {
                            // TODO возможно ненормально реализоввать js вместо рендера задач ?
                            if($this_task_id=="supertest")
                                $content .= "<script type='text/javascript'>$(document).ready(function() { $('.supertest').submit(); });</script>";
                            else
                                $content .= "<script type='text/javascript'>$(document).ready(function() { $('#$this_task_id').parent().submit(); });</script>";
                        }
                        else
                            $content .= "<script type='text/javascript'>$(document).ready(function() { $('#$resp2[first_id]').parent().submit(); });</script>";
                    }
                    else
                        $content .= "<script type='text/javascript'>$(document).ready(function() { $('#get_text_theme').click(); });</script>";

                    $content .= "</div>"; // закрыл #tt

                    // кнопка "назад к темам" и "Обнулить прогресс темы"
                    $content .= "<div class='row col-12 m-0 p-0 pl-md-5 pr-md-5 mt-3 d-flex justify-content-between'>
                                    <a class='btn col-12 col-md-3' id='back_to_themes_btn' href='/course?id=$theme->course_id'>Назад к темам</a>
                                    <button id='reset_theme' theme_id='$theme->id' class='btn col-12 col-md-4 mt-3 mt-md-0'>Обнулить прогресс темы</button>
                                 </div>";

                    $content .= "</div>"; // зкарыл #task
                }
                else
                {
                    $timer = new Timer();
                    $content = "<h2>Время решения темы истекло, возвращайтесь через</h2>";
                    $time_str = $timer->seconds_normalizations($response["sec"]);
                    $content .= "<div id='clock' class='row m-0 p-0 pr-md-5 mt-4 h2 d-flex justify-content-end blocked'>$time_str</div>";
                }

            }
            else // theme is close
                $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>Вы пока не можете решать эту тему</div>";
        }
        else
            $content = $resp["message"];
    }
    else
        header("Location: /my_courses.php");


    $file = basename(__FILE__, ".php");

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title'=>strip_tags($theme->title),
        'css'=>"/css/theme.css",
        "name"=>"<h2>$user->name</h2>",
        "content"=>$content,
        "js"=>"/js/theme.js",
        "mathjax"=>file_get_contents("templates/mathjax.html")] ;

    echo $page->render_temp();

}

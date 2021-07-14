<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Users_mistakes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;

$courses_table = new Courses_table();
$course = $courses_table->read($data["id"]);

if ($course["id"])
{
    // проверяю, есть ли курс у пользователя
    $users_courses_table = new Users_courses_table();
    $users_courses_list = $users_courses_table->read($_SESSION["id"]);
    if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$course["id"]], $users_courses_list) || $_SESSION["rights"]=="admin")
    {
        $content = "<div class='row container-fluid justify-content-center m-0 p-0'><h2>Темы</h2></div>";
        if($_SESSION["rights"]=="admin")
            $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_theme?course_id=$course[id]'>Добавить тему</a></div><br><br>";
        //беру темы курса
        $themes_table = new Themes_table();
        $themes_list = $themes_table->get_courses_themes($course["id"]);

        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($_SESSION["id"]);
        // отображаю
        for ($theme_id=0; $theme_id<count($themes_list); $theme_id++) {
            $theme = $themes_list[$theme_id];

            $class = "";

            if (in_array(["user_id" => $_SESSION["id"], "theme_id" => $theme["id"]], $users_themes_list))
                $class = "green_theme";
            else if ($theme_id == 0)
                $class = "open_theme";
            else if ($theme_id == 1)
            {
                if (in_array(["user_id" => $_SESSION["id"], "theme_id" => (int)$theme["id"]-1], $users_themes_list))
                    $class = "open_theme";
                else
                    $class = "close_theme";
            }
            else
            {
                if (in_array(["user_id" => $_SESSION["id"], "theme_id" => $theme["id"]-1], $users_themes_list))
                {
                    $close = false;
                    $tasks_table = new Tasks_table();
                    $tasks_theme_full = $tasks_table->get_tasks_theme($theme["id"]-2);
                    $tasks_ids = []; // задачи темы
                    foreach ($tasks_theme_full as $task)
                        $tasks_ids[] = $task["id"];

                    $users_mistakes_table = new Users_mistakes_table();
                    $mistakes = $users_mistakes_table->read($_SESSION["id"]); // работа над ошибками пользователя
                    foreach ($mistakes as $mistake)
                    {
                        if (in_array($mistake["task_id"], $tasks_ids))
                        {
                            $close = true;
                            break;
                        }
                    }
                    $class = $close?"close_theme":"open_theme";
                }
                else
                    $class = "close_theme";
            }
            if($_SESSION["rights"]=="admin")
                $class = "open_theme";
            $content .= "<div class='row theme $class m-0 p-0 mb-3 ml-2 mr-2 pl-2 pt-1'>";
            $content .= "<a class='text-start text-break col-12 h2 m-0 p-0' href='/theme?id=$theme[id]'>$theme[title]</a>";
            $content .= "</div><br>";
            if($_SESSION["rights"]=="admin")
            {
               $content.= "<div class='row m-0 p-0 mb-3 ml-2 mr-2'>
                                <a class='btn izm' href='/change_theme?id=$theme[id]'>Изменить</a>
                          </div>";
            }
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




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Supertests_tasks_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_GET;


$themes_table = new Themes_table();
$tmp_theme = $themes_table->read($data["id"]);

if ($tmp_theme)
{
    // проверка доступа к теме
    $users_courses_table = new Users_courses_table();
    $users_courses = $users_courses_table->read($_SESSION["id"]);
    if (in_array(["user_id" => $_SESSION["id"], "course_id" => $tmp_theme["course_id"]], $users_courses) || $_SESSION["rights"] == "admin") {

        //беру задачи темы
        $tasks_table = new Tasks_table();
        $tasks_list = $tasks_table->get_tasks_theme($tmp_theme["id"]);
        // сделанные пользователем задачи
        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($_SESSION["id"]);

        $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>";
        $content .= "<a class='btn get_text_theme mr-1' href='/theme?id=$tmp_theme[id]&text'></a>";
        // отображение квадратов задачи
        foreach ($tasks_list as $task) {
            if(isset($_GET["task_id"]))
                if ($_GET["task_id"]==$task["id"])
                    $this_task = $task;
            $button = (in_array(["user_id" => $_SESSION["id"], "task_id" => $task["id"]], $users_tasks))?"<button class='btn' id='$task[id]'></button>":"<button class='btn close_btn' id='$task[id]'></button>";

            $content .= "<form class='get_task mr-1' method='POST'>
                            <input type='hidden' name='task_id' value='$task[id]'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_task'>
                            $button
                         </form>";
        }

        // отображение супертеста
        $supertests_table = new Supertests_table();
        $tmp_sptest = $supertests_table->read_by_theme($tmp_theme["id"]);

        $content .= "<form class='get_task mr-1 supertest' method='POST'>
                            <input type='hidden' name='supertest_id' value='$tmp_sptest[id]'>
                            <input type='hidden' name='theme_id' value='$tmp_theme[id]'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_supertest'>
                            <button class='btn supertest'></button>
                         </form>";

        // кнопка "добавить задачу"
        if ($_SESSION["rights"]=="admin")
            $content .="<a class='btn ml-3 create add_task' href='/add_task?theme_id=$tmp_theme[id]'>Добавить задачу</a>";

        $content .= "</div><br><br>" ; // закрыл блок с квадратами задач

        if(count($tasks_list))
        {
            if(isset($_GET["text"]))
                $content .="<div id='task'><div class='row m-0 p-0 justify-content-center h2'>Описание темы</div><br><div class='row m-0 p-0 justify-content-center h2'>$tmp_theme[text]</div></div>";
            else
            {
                $this_task = $tasks_list[0];
                $task_block = new Render();
                $content .="<div id='task'>";
                $content .= $task_block->render_task($this_task);
                $content .= "</div><br>";
            }
        }
        else
            $content .="<div id='task'><div class='row m-0 p-0 justify-content-center h2'>Описание темы</div><br><div class='row m-0 p-0 justify-content-center h2'>$tmp_theme[text]</div></div>";

        $content .= "<div class='h2 d-flex justify-content-center' id='message'></div>";
    } else
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

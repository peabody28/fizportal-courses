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
if(isset($_POST["submit"]))
{
    if($_POST["code"]=="get_theme_text")
    {
        $tmp_theme = $themes_table->read($_POST["theme_id"]);
        echo json_encode(["text"=>$tmp_theme["text"]]);
        exit();
    }
}

$tmp_theme = $themes_table->read($data["id"]);
if ($tmp_theme)
{
    $content = $tmp_theme["text"];
    if($_SESSION["rights"]=="admin")
       $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/add_task?theme_id=$tmp_theme[id]'>Добавить задачу</a> </div><br><br>";

    $users_courses_table = new Users_courses_table();
    $users_courses = $users_courses_table->read($_SESSION["id"]);
    if (in_array(["user_id" => $_SESSION["id"], "course_id" => $tmp_theme["course_id"]], $users_courses) || $_SESSION["rights"] == "admin") {
        $tasks_table = new Tasks_table();
        //беру задачи темы
        $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>";
        $tasks_list = $tasks_table->get_tasks_theme($tmp_theme["id"]);

        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($_SESSION["id"]);
        $content .= "<a class='btn get_text_theme mr-1' href='/theme?id=$tmp_theme[id]'></a>";
        foreach ($tasks_list as $task) {
            if(isset($_GET["task_id"]))
                if ($_GET["task_id"]==$task["id"])
                    $this_task = $task;
            if (in_array(["user_id" => $_SESSION["id"], "task_id" => $task["id"]], $users_tasks))
                $button = "<a class='btn' id='$task[id]' href='/theme?id=$tmp_theme[id]&task_id=$task[id]'></a>";
            else
                $button = "<a class='btn close_btn' id='$task[id]' href='/theme?id=$tmp_theme[id]&task_id=$task[id]'></a>";

            $content .= "<form class='get_task mr-1' method='POST'>
                            <input type='hidden' name='task_id' value='$task[id]'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_task'>
                            $button
                         </form>  ";
        }
        // супертест
        $supertests_table = new Supertests_table();
        $tmp_sptest = $supertests_table->read_by_theme($tmp_theme["id"]);
        $content .= "<a class='btn close_btn supertest' href='/theme?id=$tmp_theme[id]&supertest'></a>";

        // добавить задачу
        if ($_SESSION["rights"]=="admin")
            $content .="<a class='btn ml-3 create add_task' href='/add_task?theme_id=$tmp_theme[id]'>Добавить задачу</a>";

        // задача или супертест или описание
        $content .= "</div><br><br>" ;

        if(count($tasks_list) || isset($_GET["supertest"]))
        {
            if(isset($_GET["task_id"]))
            {
                if(!$this_task)
                {
                    header("Location: /theme?id=$tmp_theme[id]");
                    exit();
                }
                $task_block = new Render();
                $content .="<div id='task'>";
                $content .= $task_block->render_task($this_task);
                $content .= "</div><br>";// закрыл блок #task
            }
            else if(isset($_GET["supertest"]))
            {
                // беру задачи супертеста
                $supertests_table = new Supertests_table();
                $tmp_sp_test = $supertests_table->read_by_theme($_GET["id"]);

                $supertests_tasks_table = new Supertests_tasks_table();
                $supertests_tasks_rows = $supertests_tasks_table->read($tmp_sp_test["id"]);

                $supertests_tasks = [];
                $tasks_table = new Tasks_table();

                foreach ($supertests_tasks_rows as $row)
                    array_push($supertests_tasks, $tasks_table->read($row["task_id"]));

                $supertest = new Render();
                if ($_SESSION["rights"]=="admin")
                    $content .= "<div class='row justify-content-center'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$tmp_sp_test[id]'>Добавить задачу в супертест</a></div><br><br>";
                $content .="<div id='task'>";
                $content .= $supertest->render_supertest($tmp_sp_test["id"], $supertests_tasks);
                $content .= "</div><br>";
            }
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

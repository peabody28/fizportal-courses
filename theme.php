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
        $content .= "<form class='get_theme_text mr-1' method='POST'>
                            <input type='hidden' name='theme_id' value='$tmp_theme[id]'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_theme_text'>
                            <button class='btn' id='get_theme_btn'></button>
                     </form>";
        foreach ($tasks_list as $task) {
            if (in_array(["user_id" => $_SESSION["id"], "task_id" => $task["id"]], $users_tasks))
                $button = "<button class='btn' id='$task[id]'></button>";
            else
                $button = "<button class='btn close_btn' id='$task[id]'></button>";

            $content .= "<form class='get_task mr-1' method='POST'>
                            <input type='hidden' name='task_id' value='$task[id]'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_task_data'>
                            $button
                         </form>  ";
        }
        $first_task = $tasks_list[0];
        $content .= "</div><br><br>" ;
        $content .="<div id='task'>";
        $content .=
            "<div class='row m-0 p-0 justify-content-center h2'>Условие</div>
            <br>
            <div class='opis m-0 p-0 d-flex justify-content-center'>$first_task[text]</div>
            <br>
            <br> 
            <div class='container-fluid d-flex justify-content-center m-0 p-0'>
                <form class='send_answer' method='POST' onsubmit='send_answer();return false;'>
                    <input type='hidden' name='submit' >
                    <input type='hidden' name='task_id' value='$first_task[id]'>
                    <input type='hidden' name='code' value='send_answer'>
                    <input type='text' class='row' name='answer'><br>
                    <div class='row d-flex justify-content-center'><button class='btn' type='submit'>Отправить</button></div>
                </form>
            </div>
            <br><br>";
        if ($_SESSION["rights"]=="admin")
        {
            $content .= "<form class='del_task' method='POST' onsubmit='del_task();return false;'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='task_id' value='$first_task[id]'>
            <input type='hidden' name='code' value='del_task'>
            <div class='row d-flex justify-content-center'><button class='btn delete' type='submit'>Удалить эту задачу</button></div>
            </form>";
        }
        $content .= "</div><br>"; // закрыл блок #task
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

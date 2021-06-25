<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks.php";
require_once __DIR__."/classes/Users_tasks_table.php";
session_start();

$data=$_POST;
if(isset($data["submit"]))
{
    $tasks_table = new Tasks_table();
    $task = $tasks_table->read($data["task_id"]);
    if($task["answer"] == $data["answer"])
    {
        $users_tasks = new Users_tasks();
        $users_tasks->user_id = $_SESSION["id"];
        $users_tasks->task_id = $data["task_id"];

        $users_tasks_table = new Users_tasks_table();
        $users_tasks_table->create($users_tasks);
        echo json_encode(["status"=>"OK"]);
    }
    else
    {
        echo json_encode(["status"=>"ERROR"]);
    }

}
else
{
    $tasks_table = new Tasks_table();
    $task = $tasks_table->read($_GET["id"]);
    if($task["id"])
    {
        $task_block = new Render();
        $content = $task_block->render_full_task($task);

        $page = new Render();
        $page->temp = 'main.html';
        $page->argv = ['title'=>"task $task[id]",
            'css'=>"/css/task.css",
            "name"=>"<h2>$_SESSION[name]</h2>",
            "content"=>$content,
            "js"=>"/js/task.js"] ;

        echo $page->render_temp();
    }
    else
        header("Location: /courses.php");
}

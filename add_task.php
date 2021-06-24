<?php
session_start();
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Render.php";

$data = $_POST;

if(isset($data["submit"]))
{
    $task = new Task();
    $task->text = $data["task_text"];
    $task->answer = $data["task_answer"];
    $task->theme_id = $data["theme_id"];
    $task->image_url = ($data["image_url"]=="")?null:$data["image_url"];
    $task->complexity = $data["task_complexity"];
    $tasks_table = new Tasks_table();
    $response = $tasks_table->create($task);
    echo json_encode(["theme_id"=>$data["theme_id"]]); // для перехода к дальнейшему редактированию темы
}
else
{

    $add_task_block = new Render();
    $add_task_block->temp = "add_task_form.html";
    $add_task_block->argv = ["theme_id"=>$_GET["theme_id"]];
    $content = $add_task_block->render_temp();
    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "add_task",
        'css' => "/css/add_task.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/add_task.js"];
    echo $page->render_temp();
}


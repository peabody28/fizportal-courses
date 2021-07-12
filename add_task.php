<?php
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__ . "/classes/Tasks_answer.php";
require_once __DIR__."/classes/Tasks_answers_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    $task = new Task();
    $task->text = addslashes($data["task_text"]);
    $task->type = $data["type"];
    if($data["type"]=="B")
        $task->answer = $data["task_answer"];
    $task->theme_id = $data["theme_id"];
    $task->complexity = $data["task_complexity"];

    // TODO: ВОЗМОЖНО ЗДЕСЬ НУЖНА ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ
    $tasks_table = new Tasks_table();
    $response = $tasks_table->create($task);

    if($data["type"]=="A" && $response)
    {
        $tasks_answer = new Tasks_answer();
        $tasks_answers_table = new Tasks_answers_table();
        $tasks_answer->task_id = $task->id;
        for($i=1; $i<=5;$i++)
        {
            if(isset($data["answ".$i]))
            {
                $tasks_answer->answer=$data["answ".$i];
                $tasks_answers_table->create($tasks_answer);
            }
        }
    }
    echo json_encode(["theme_id"=>$data["theme_id"]]);
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


<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__ . "/classes/Task.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Tasks_answers_table.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Supertests_tasks_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    $task = new Task();
    $task->text = $data["task_text"];
    $task->type = $data["type"];
    if($data["type"]=="B")
        $task->answer = $data["task_answer"];
    if ($data["theme_id"])
        $task->theme_id = $data["theme_id"];
    $task->complexity = $data["task_complexity"];

    // TODO: ВОЗМОЖНО ЗДЕСЬ НУЖНА ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ
    $tasks_table = new Tasks_table();
    $response = $tasks_table->create($task);

    if($data["type"]=="A" && $response)
    {
        $tasks_answers_table = new Tasks_answers_table();
        for($i=1; $i<=5;$i++)
        {
            if(isset($data["answ".$i]))
                $tasks_answers_table->create(["task_id"=>$task->id, "answer"=>$data["answ".$i]]);
        }
    }
    if ($data["theme_id"])
        echo json_encode(["theme_id"=>$data["theme_id"]]);
    else
    {
        $supertests_table = new Supertests_table();
        $tmp_sp_test = $supertests_table->read($data["supertest_id"]);

        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_table->create(["id"=>$data["supertest_id"], "task_id"=>$task->id]);

        echo json_encode(["theme_id"=>$tmp_sp_test["theme_id"], "supertest_id"=>$data["supertest_id"]]);
    }

}
else
{
    $add_task_block = new Render();
    $add_task_block->temp = "add_task_form.html";
    if (!isset($_GET["supertest_id"]))
        $add_task_block->argv = ["theme_id"=>$_GET["theme_id"], "supertest_id"=>0];
    else
        $add_task_block->argv = ["theme_id"=>0, "supertest_id"=>$_GET["supertest_id"]];
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


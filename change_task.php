<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/auth_root.php";
require_once __DIR__ . "/classes/Task.php";
require_once __DIR__ . "/classes/Tasks_table.php";
require_once __DIR__ . "/classes/Tasks_answer.php";
require_once __DIR__ . "/classes/Tasks_answers_table.php";
require_once __DIR__ . "/classes/Render.php";

session_start();


$data = $_POST;

if (isset($data["submit"])) {

    $task = new Task();
    $task->id = $data["task_id"];

    if($data["code"]=="change_text")
    {
        $task->text = $data["new_task_text"];
        $tasks_table = new Tasks_table();
        $tasks_table->update($task, "text");
        echo json_encode(["status"=>"OK", "message"=>"Условие изменено"]);
    }
    else if($data["code"]=="change_answer")
    {
        $tasks_table = new Tasks_table();
        if($data["type"]=="B")
        {
            $task->type = "B";
            $task->answer = strip_tags($data["new_task_answer"]);
            $tasks_table->update($task, "type");
            $tasks_table->update($task, "answer");

            $tasks_answer = new Tasks_answer();
            $tasks_answers_table = new Tasks_answers_table();
            $tasks_answers_table->delete($task->id);
        }
        else
        {
            $task->type = "A";
            $task->answer = null;
            $tasks_table->update($task, "type");
            $tasks_table->update($task, "answer");

            // добавляю ответы в таблицу ответов части А
            $tasks_answer = new Tasks_answer();
            $tasks_answers_table = new Tasks_answers_table();
            $tasks_answers_table->delete($task->id);
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
        echo json_encode(["status"=>"OK", "message"=>"Ответ изменен"]);
    }
    else if($data["code"]=="change_complexity")
    {
        $task->complexity = $data["new_task_comlexity"];
        $tasks_table = new Tasks_table();
        $tasks_table->update($task, "complexity");
        echo json_encode(["status"=>"OK", "message"=>"Сложность изменена"]);
    }
    else if($data["code"]=="del_task")
    {
        $tasks_table = new Tasks_table();
        $tasks_table->delete($task->id);
        echo json_encode(["status"=>"OK"]);
    }

} else {
    $tasks_table = new Tasks_table();
    $tmp_task = $tasks_table->read($_GET["id"]);

    $forms = new Render();
    $forms->temp = "change_task_forms.html";
    $forms->argv = ["task_id"=>$tmp_task["id"], "task_text"=>$tmp_task["text"], "task_answer"=>$tmp_task["answer"], "task_complexity"=>$tmp_task["complexity"]];


    $content = $forms->render_temp();

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_task",
        'css' => "/css/change_theme.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_task.js"];
    echo $page->render_temp();

}

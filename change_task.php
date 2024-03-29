<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/auth_root.php";
require_once __DIR__ . "/classes/Task.php";
require_once __DIR__ . "/classes/Tasks_table.php";
require_once __DIR__ . "/classes/Tasks_answers_table.php";
require_once __DIR__ . "/classes/Render.php";

session_start();


$data = $_POST;

if (isset($data["submit"])) {

    $task = new Task($data["task_id"]);

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
            $tasks_answers_table = new Tasks_answers_table();
            $tasks_answers_table->delete($task->id);
            for($i=1; $i<=5;$i++)
            {
                if(isset($data["answ".$i]))
                    $tasks_answers_table->create(["task_id"=>$task->id, "answer"=>$data["answ".$i]]);
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
        $tmp_task = $tasks_table->read($task->id);
        echo json_encode(["status"=>"OK"]);
    }
    else if($data["code"]=="change_image")
    {
        $uploaddir = __DIR__.'/media/tasks_imgs/';

        $apend= "task".$task->id.'.jpg';

        $uploadfile = "$uploaddir$apend";



        if($_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg')
        {
            $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
            if ($status)
            {
                $task->img_url = '/media/tasks_imgs/'.$apend;
                $tasks_table = new Tasks_table();
                $tasks_table->update($task, "img_url");
                header("Location: /theme?id=$data[theme_id]");
            }
            else
                header("Location: /theme?id=$data[theme_id]");
        }
        else
            header("Location: /theme?id=$data[theme_id]");

    }

} else {
    $task = new Task($_GET["id"]);

    $back_id = $task->id; // ID задачи куда возвращаться
    if(isset($_GET["from_supertest"]))
    {
        $back_id = "supertest";
        $tmp_task["theme_id"]=$_GET["theme_id"];
    }


    $forms = new Render();
    $forms->temp = "change_task_forms.html";
    if($task->type == "A")
        $task->get_A_answer();


    $forms->argv = ["task_id"=>$task->id, "task_text"=>$task->text, "task_answer"=>$task->answer, "task_complexity"=>$task->complexity, "theme_id" => $task->theme_id, "back_id"=>$back_id];
    foreach ($task->answer as $answ)
        $forms->argv["a$answ"."_checked"] = "checked";

    $content = $forms->render_temp();

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_task",
        'css' => "/css/change_task.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_task.js"];
    echo $page->render_temp();

}

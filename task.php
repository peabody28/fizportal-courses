<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Tasks_answers_table.php";
session_start();


$data=$_POST;

if(isset($data["submit"]))
{
    if($data["code"]=="get_task")
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($data["task_id"]);
        $task_block = new Render();
        echo json_encode(["block"=>$task_block->render_task($task)]);
    }
    else if ($data["code"]=="send_answer")
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($data["task_id"]);
        $status = true;

        if ($task["type"]=="A") {
            $tasks_answers_table = new Tasks_answers_table();
            $answers = $tasks_answers_table->read($task["id"]);
            for($i=1; $i<=5;$i++)
            {
                if (isset($data["answ".$i]))
                {
                    if(!in_array(["task_id"=>$task["id"], "answer"=>$data["answ".$i]], $answers))
                    {
                        $status=false;
                        break;
                    }
                }
                else
                {
                    if(in_array(["task_id"=>$task["id"], "answer"=>$i], $answers))
                    {
                        $status=false;
                        break;
                    }
                }
            }
        }
        else
            $status = ($data["answer"]==$task["answer"]);

        if($status)
        {
            $users_tasks = new Users_tasks();
            $users_tasks->user_id = $_SESSION["id"];
            $users_tasks->task_id = $data["task_id"];

            $users_tasks_table = new Users_tasks_table();
            $users_tasks_table->create($users_tasks);
            echo json_encode(["status" => "OK", "task_id"=>$data["task_id"]]);
        }
        else
        {
            // TODO: Добавление задачи в работу над ошибками
            echo json_encode(["status" => "ERROR"]);
        }

    }
    else if($data["code"]=="get_task_block")
    {
        $task_block = new Render();
        echo json_encode($task_block->render_task(json_decode($data["task"])));
    }
    else
        echo json_encode(["status"=>"wrong code"]);

}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


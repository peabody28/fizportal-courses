<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks.php";
require_once __DIR__."/classes/Users_tasks_table.php";
session_start();


$data=$_POST;

if(isset($data["submit"]))
{
    if($data["code"]=="get_task_data")
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($data["task_id"]);
        echo json_encode($task);
    }
    else if ($data["code"]=="send_answer")
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($data["task_id"]);
        if ($task["answer"] == $data["answer"]) {
            $users_tasks = new Users_tasks();
            $users_tasks->user_id = $_SESSION["id"];
            $users_tasks->task_id = $data["task_id"];

            $users_tasks_table = new Users_tasks_table();
            $users_tasks_table->create($users_tasks);
            echo json_encode(["status" => "OK", "task_id"=>$data["task_id"]]);
        } else {
            // TODO: Добавление задачи в работу над ошибками
            echo json_encode(["status" => "ERROR"]);
        }
    }
    else
        echo json_encode(["status"=>"wrong code"]);

}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


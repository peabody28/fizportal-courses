<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Tasks_answers_table.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Professor.php";
session_start();


$data=$_POST;
$tasks_table = new Tasks_table();


function construct_task($id)
{
    global $tasks_table;
    global $data;

    $tmp_task = $tasks_table->read($id);

    $task = ["id"=>$tmp_task["id"], "type"=>$tmp_task["type"]];
    if($tmp_task["type"]=="A")
    {
        $tasks_answers_table = new Tasks_answers_table();
        $task["answers"] = $tasks_answers_table->read($task["id"]);

        $task["user_answers"] = [];
        for($i=1; $i<=5; $i++)
            if (isset($data["$tmp_task[id]_a_answ$i"]))
                $task["user_answers"][] = $i;
    }
    else
    {
        $task["answer"] =  $tmp_task["answer"];
        $task["user_answer"]=$data["$tmp_task[id]_b_answer"];
    }
    return $task;
}


if(isset($data["submit"]))
{
    if ($data["code"]=="send_answer")
    {
        $task = construct_task($data["task_id"]);

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            $users_tasks_table = new Users_tasks_table();
            $users_tasks_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$data["task_id"]]);
            echo json_encode(["status" => "OK", "task_id"=>$data["task_id"]]);
        }
        else
        {
            // TODO: Добавление задачи в работу над ошибками
            echo json_encode(["status" => "ERROR"]);
        }
    }
    else if($data["code"]=="send_supertest_answers")
    {
        $str = "";
        foreach ($data as $key => $val)
        {
            if($key=="code" || $key == "submit")
                continue;
            $str .= "&".$key."=";
        }
        $match = [];
        preg_match_all("/&([0-9]*)_{1}a{0,1}b{0,1}_{1}/u", $str, $match);

        $tasks = [];

        foreach (array_unique($match[1]) as $task_id)
        {
            $task = construct_task($task_id);
            array_push($tasks, $task);
        }

        // проверка
        $status = true;
        $prof = new Professor();
        foreach ($tasks as $item)
        {
            $status = $prof->check_task($item);
            if(!$status)
                break;
        }
        if ($status)
        {
            $users_themes_table = new Users_themes_table();
            $users_themes_table->create(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]]);
            echo json_encode(["status"=>"OK"]);
        }
        else
            echo json_encode(["status"=>"ERROR"]);

    }
    else if($data["code"]=="get_task_block")
    {

    }
    else
        echo json_encode(["status"=>"wrong code"]);
}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


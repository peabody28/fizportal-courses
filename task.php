<?php
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Tasks_table.php";

require_once __DIR__."/classes/Task_handler.php";
require_once __DIR__."/classes/Mistake_handler.php";
require_once __DIR__."/classes/Supertest_handler.php";

require_once __DIR__."/classes/Supertests_tasks_table.php";
require_once __DIR__."/classes/Users_progress_theme_table.php";
require_once __DIR__."/classes/Professor.php";

require_once __DIR__."/classes/Tasks_block_constructor.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data=$_POST;
$tasks_table = new Tasks_table();


if(isset($data["submit"]))
{

    if ($data["code"]=="send_answer")
    {
        $task_handler = new Task_handler();
        $task_handler->data = $data;

        $user = new User($_SESSION["id"]);
        $task_handler->data["user"]=$user;

        $resp = $task_handler->send_answer();
        echo json_encode($resp);
    }
    else if($data["code"]=="send_mistake_answer")
    {
        $mistakes_handler = new Mistake_handler();
        $mistakes_handler->data = $data;
        $resp = $mistakes_handler->send_answer();
        echo json_encode($resp);
    }
    else if($data["code"]=="send_supertest_answers")
    {
        $supertest_handler = new Supertest_handler();
        $supertest_handler->data = $data;
        $resp = $supertest_handler->send_answer();
        echo json_encode($resp);
    }
    else if($data["code"]=="get_text_theme")
    {
        $tasks_block_constructor = new Tasks_block_constructor();
        $response = $tasks_block_constructor->get_text_theme_block($data["theme_id"]);
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_task")
    {
        $tasks_block_constructor = new Tasks_block_constructor();
        $response = $tasks_block_constructor->get_task_block($data["task_id"], $data["next_task_id"], ($_SESSION["rights"]=="admin"));
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_mistake")
    {
        $tasks_block_constructor = new Tasks_block_constructor();
        $response = $tasks_block_constructor->get_mistake_block($data["task_id"], $data["next_task_id"]);
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_supertest")
    {
        $tasks_block_constructor = new Tasks_block_constructor();
        $resp = $tasks_block_constructor->get_supertest_block($_SESSION["id"], $data["theme_id"], ($_SESSION["rights"]=="admin"), $data["supertest_id"]);
        echo json_encode(["block" => $resp["block"]]);
    }
    else
        echo json_encode(["status"=>"wrong code"]);
}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


<?php
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Supertest.php";
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Mistake.php";


require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/User.php";

require_once __DIR__."/classes/Tasks_table.php";

//require_once __DIR__."/classes/Task_handler.php";
//require_once __DIR__."/classes/Mistake_handler.php";
//require_once __DIR__."/classes/Supertest_handler.php";
session_start();


$data=$_POST;
$tasks_table = new Tasks_table();


if(isset($data["submit"]))
{

    if ($data["code"]=="send_answer")
    {

        $data["user"] = new User($_SESSION["id"]);
        $task = new Task($data["task_id"]);
        $resp = $task->send_answer($data);
        echo json_encode($resp);

        //$task_handler = new Task_handler();
        //$task_handler->data = $data;
        //$task_handler->data["user"]=$user;
        //$resp = $task_handler->send_answer();

    }
    else if($data["code"]=="send_mistake_answer")
    {
        $data["user"] = new User($_SESSION["id"]);
        $mistake = new Mistake($data["task_id"]);
        $resp = $mistake->send_answer($data);
        echo json_encode($resp);

        //$mistakes_handler = new Mistake_handler();
        //$mistakes_handler->data = $data;
        //$mistakes_handler->data["user"] = new User($_SESSION["id"]);
        //$resp = $mistakes_handler->send_answer();
    }
    else if($data["code"]=="send_supertest_answers")
    {
        $data["user"] = new User($_SESSION["id"]);
        $supertest = new Supertest($data["theme_id"]);
        $resp = $supertest->send_answer($data);
        echo json_encode($resp);


        //$supertest_handler = new Supertest_handler();
        //$supertest_handler->data = $data;
        //$supertest_handler->data["user"] = new User($_SESSION["id"]);
        //$resp = $supertest_handler->send_answer();
        //echo json_encode($resp);
    }
    else if($data["code"]=="get_text_theme")
    {
        $theme = new Theme($data["theme_id"]);
        $response = $theme->get_text_html();
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_task")
    {
        $task = new Task($data["task_id"]);
        $response = $task->get_html(["is_admin"=>$_SESSION["rights"]=="admin"]);
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_mistake")
    {
        $mistake = new Mistake($data["task_id"]);
        $response = $mistake->get_html(["is_admin"=>$_SESSION["rights"]=="admin"]);
        echo json_encode(["block"=>$response["block"]]);
    }
    else if ($data["code"]=="get_supertest")
    {

        $user = new User($data["user_id"]);
        $theme = new Theme($data["theme_id"]);

        // проверка на доступность супертеста
        $professor = new Professor();
        $resp = $professor->check_access_supertest($user, $theme);
        if(!$resp["status"])
            echo json_encode(["block" => $resp["error"]]);

        $supertest = new Supertest($theme->id);

        $resp = $supertest->get_html($data);
        echo json_encode(["block" => $resp["block"]]);
    }
    else
        echo json_encode(["status"=>"wrong code"]);
}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


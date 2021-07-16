<?php
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Users_tasks_table.php";
require_once __DIR__."/classes/Supertests_table.php";
require_once __DIR__."/classes/Supertests_tasks_table.php";
require_once __DIR__."/classes/Tasks_answers_table.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Users_progress_theme_table.php";
require_once __DIR__."/classes/Users_mistakes_table.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Task_handler.php";
require_once __DIR__."/classes/Users_themes_time.php";
session_start();


$data=$_POST;
$tasks_table = new Tasks_table();


if(isset($data["submit"]))
{
    $task_handler = new Task_handler();
    $task_handler->data = $data;

    if ($data["code"]=="send_answer")
    {
        $resp = $task_handler->send_answer();
        echo json_encode($resp);
    }
    else if($data["code"]=="send_mistake_answer")
    {
        $resp = $task_handler->send_mistake_answer();
        echo json_encode($resp);
    }
    else if($data["code"]=="send_supertest_answers")
    {
        $resp = $task_handler->send_supertest_answer();
        echo json_encode($resp);
    }// class Task_view
    else if ($data["code"]=="get_task")
    {
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($data["task_id"]);

        $block = new Render();

        $task_block = $block->render_task($tmp_task);
        if ($_SESSION["rights"]=="admin")
        {
            $task_block .= "<div class='row justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$data[task_id]'>Изменить задачу</a></div><br><br>";
            $task_block .= " <div class='row d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($data[task_id]);return false;'>Удалить эту задачу</button>
                             </div><br><br>";
        }
        // материалы для задачи
        $task_block .= "<div class='row justify-content-center h2' id='message'></div>";
        $task_block .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$data[task_id]'>Материалы для задачи</a></div>";

        echo json_encode(["block"=>$task_block]);
    }
    else if ($data["code"]=="get_mistake")
    {
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($data["task_id"]);

        $block = new Render();

        $task_block = $block->render_mistake($tmp_task);
        // материалы для задачи
        $task_block .= "<div class='row justify-content-center h2' id='message'></div>";
        $task_block .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$data[task_id]'>Материалы для задачи</a></div>";

        echo json_encode(["block"=>$task_block]);
    }
    else if ($data["code"]=="get_supertest")
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]]);

        if((int)$users_progress["progress"]<10 && $_SESSION["rights"]!="admin")
        {
            $progress = $users_progress["progress"]?:"0";
            echo json_encode(["block" => "Вы решили мало задач ваш балл ".$progress."/10"]);
            exit();
        }
        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_rows = $supertests_tasks_table->read($data["supertest_id"]);

        $tasks_table = new Tasks_table();

        // отображение задач супертеста
        $supertest = new Render();
        $supertests_block = "";
        if ($_SESSION["rights"] == "admin")
            $supertests_block .= "<div class='row justify-content-center'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$data[supertest_id]'>Добавить задачу в супертест</a></div><br><br>";

        if ($supertests_tasks_rows)
        {
            $supertests_block .=
                "<form class='send_answer' method='POST' onsubmit='send_answer();return false;'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='send_supertest_answers'>
                                    <input type='hidden' name='theme_id' value='$data[theme_id]'>";
            foreach ($supertests_tasks_rows as $row)
            {
                $task = $tasks_table->read($row["task_id"]);
                $supertests_block .= $supertest->render_supertest_task($task);
                if ($_SESSION["rights"] == "admin")
                {
                    $supertests_block .= "<div class='row justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task[id]'>Изменить задачу</a></div><br><br>";
                    $supertests_block .= " <div class='row d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task[id]);return false;'>Удалить эту задачу</button>
                                           </div><br><br>";
                }
            }
        }
            $supertests_block .= "<div class='row justify-content-center h2' id='message'></div>";
            $supertests_block .= "<div class='row m-0 col-12 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>";
            $supertests_block .= "</form><br>";

        echo json_encode(["block" => $supertests_block]);
    }
    else
        echo json_encode(["status"=>"wrong code"]);
}
else
{
    echo json_encode(["status"=>"error1 not subm"]);
}


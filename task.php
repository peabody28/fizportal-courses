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
            // добавляю задачу в список решенных пользователем
            $users_tasks_table = new Users_tasks_table();
            $st = $users_tasks_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$data["task_id"]]);
            if($st) // если решается впервые
            {
                // добавляю балл
                $users_progress_theme_table = new Users_progress_theme_table();
                $users_progress_theme_table->add_point(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]]);
            }
            echo json_encode(["status" => "OK", "task_id"=>$data["task_id"]]);
        }
        else
        {
            $users_tasks_table = new Users_tasks_table();
            $users_tasks = $users_tasks_table->read($_SESSION["id"]);

            if(!in_array(["user_id"=>$_SESSION["id"], "task_id"=>$data["task_id"]], $users_tasks)) // если пользователь эту задачу еще не решал
            {
                // добавляю задачу в РО
                $users_mistakes_table = new Users_mistakes_table();
                $st = $users_mistakes_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$data["task_id"]]);
                // если $st==true то задачу первый раз решили неверно и я могу снять балл
                $users_themes_table = new Users_themes_table();
                $users_themes = $users_themes_table->read($_SESSION["id"]);

                if($st && !in_array(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]], $users_themes)) // если тема не выполнена
                {
                    // Cнимаю балл за неверное решение
                    $users_progress_theme_table = new Users_progress_theme_table();
                    $users_progress_theme_table->delete_point(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]]);
                }

            }
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
        $task_block .= "<div class='row justify-content-center h2' id='message'></div>";
        // материалы для задачи
        $task_block .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$data[task_id]'>Материалы для задачи</a></div>";

        echo json_encode(["block"=>$task_block]);
    }
    else if ($data["code"]=="get_supertest")
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$data["theme_id"]]);

        if((int)$users_progress["progress"]<10 && $_SESSION["rights"]!="admin")
        {
            echo json_encode(["block" => "Вы решили мало задач ваш балл ".$users_progress["progress"]."/10"]);
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


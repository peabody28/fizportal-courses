<?php
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Render.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Supertests_tasks_table.php";
require_once __DIR__."/Professor.php";



class Tasks_block_constructor
{
    public function get_task_block($id, $is_admin=false)
    {
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($id);

        $block = new Render();

        $task_block = $block->render_task($tmp_task);
        if ($is_admin)
        {
            $task_block .= "<div class='row justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$id'>Изменить задачу</a></div><br><br>";
            $task_block .= " <div class='row d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($id);return false;'>Удалить эту задачу</button>
                             </div><br><br>";
        }
        // материалы для задачи
        $task_block .= "<div class='row justify-content-center h2' id='message'></div>";
        $task_block .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$id'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];

    }

    public function get_mistake_block($id)
    {
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($id);

        $block = new Render();

        $task_block = $block->render_mistake($tmp_task);
        // материалы для задачи
        $task_block .= "<div class='row justify-content-center h2' id='message'></div>";
        $task_block .= "<br><br><div class='row justify-content-center'> <a href='/materials?task_id=$id'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];
    }

    public function get_supertest_block($user_id, $theme_id, $is_admin=false, $sptest_id)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$user_id, "theme_id"=>$theme_id]);

        $professor = new Professor();
        $resp = $professor->check_access_supertest($users_progress, $is_admin);
        if(!$resp["status"])
            return ["block"=>$resp["error"]];

        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_rows = $supertests_tasks_table->read($sptest_id);

        $tasks_table = new Tasks_table();

        // отображение задач супертеста
        $supertest = new Render();
        $supertests_block = "";
        if ($is_admin)
            $supertests_block .= "<div class='row justify-content-center'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$sptest_id'>Добавить задачу в супертест</a></div><br><br>";

        if ($supertests_tasks_rows)
        {
            $supertests_block .=
                "<form class='send_answer' method='POST' onsubmit='send_answer();return false;'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='send_supertest_answers'>
                                    <input type='hidden' name='theme_id' value='$theme_id'>";
            foreach ($supertests_tasks_rows as $row)
            {
                $task = $tasks_table->read($row["task_id"]);
                $supertests_block .= $supertest->render_supertest_task($task);
                if ($is_admin)
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
        return ["block"=>$supertests_block];
    }
}
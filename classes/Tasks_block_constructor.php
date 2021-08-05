<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Themes_points_limit_table.php";
require_once __DIR__."/Render.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Supertests_tasks_table.php";
require_once __DIR__."/Professor.php";



class Tasks_block_constructor
{
    public function get_text_theme_block($id)
    {
        $themes_table = new Themes_table();
        $tmp_theme = $themes_table->read($id);

        $block = "<div class='col-12 m-0 p-0 d-flex justify-content-center'>Описание темы</div><div class='col-12 m-0 p-0 text-break'>$tmp_theme[text]</div>";
        return ["block"=>$block];

    }

    public function get_task_block($id, $next_id=null, $is_admin=false)
    {
        $task = new Task($id);

        $block = new Render();

        $task_block = $block->render_task($task, $next_id, 0);
        if ($is_admin)
        {
            $task_block .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task->id'>Изменить задачу</a></div>";
            $task_block .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task->id);return false;'>Удалить эту задачу</button>
                             </div>";
        }
        // материалы для задачи
        $task_block .= "<div class='d-flex justify-content-center col-12 mt-3' id='message'></div>";
        $task_block .= "<div class='col-12 d-flex justify-content-center'> <a href='/materials?task_id=$task->id'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];

    }

    public function get_mistake_block($id, $next_id=null)
    {
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($id);

        $block = new Render();

        $task_block = $block->render_mistake($tmp_task, $next_id);
        // материалы для задачи
        $task_block .= "<div class='h2 d-flex justify-content-center col-12 mt-3' id='message'></div>";
        $task_block .= "<div class='col-12 mt-5 d-flex justify-content-center'> <a href='/materials?task_id=$tmp_task[id]'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];
    }

    public function get_supertest_block($user_id, $theme_id, $is_admin=false, $sptest_id)
    {
        $themes_points_limit_table = new Themes_points_limit_table();
        $resp = $themes_points_limit_table->read($theme_id);
        $limits_of_points = $resp["points_limit"]?:10; // если лимит не установен, принимаем его за 10 баллов

        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$user_id, "theme_id"=>$theme_id]);

        $professor = new Professor();
        $resp = $professor->check_access_supertest($limits_of_points, $users_progress, $is_admin);
        if(!$resp["status"])
            return ["block"=>$resp["error"]];

        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_rows = $supertests_tasks_table->read($sptest_id);

        $tasks_table = new Tasks_table();

        // отображение задач супертеста
        $supertest = new Render();
        $supertests_block = "";
        if ($is_admin)
            $supertests_block .= "<div class='col-12 d-flex justify-content-center mb-5'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$sptest_id'>Добавить задачу в супертест</a></div>";
        if ($supertests_tasks_rows)
        {
            $supertests_block .=
                "<form class='col-12 send_supertest_answers'  method='POST' onsubmit='send_supertest_answers();return false;'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='send_supertest_answers'>
                                    <input type='hidden' name='theme_id' value='$theme_id'>";
            foreach ($supertests_tasks_rows as $row)
            {
                $task = $tasks_table->read($row["task_id"]);
                $supertests_block .= $supertest->render_supertest_task($task);
                if ($is_admin)
                {
                    $supertests_block .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task[id]&from_supertest=true&theme_id=$theme_id'>Изменить задачу</a></div>";
                    $supertests_block .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task[id]);return false;'>Удалить эту задачу</button>
                                           </div>";
                }
            }
        }
        $supertests_block .= "<hr><div class='m-0 col-12 mt-5 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>";
        $supertests_block .= "<div class='col-12 mt-3 d-flex justify-content-center h2' id='message'></div>";
        $supertests_block .= "</form>";
        return ["block"=>$supertests_block];
    }
}

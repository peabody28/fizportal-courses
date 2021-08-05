<?php
require_once __DIR__."/Tasks_block_constructor.php";


class Supertest_block_constructor extends Tasks_block_constructor
{
    public function get_supertest_block($user_id, $theme_id, $sptest_id, $is_admin=false)
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
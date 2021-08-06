<?php
require_once __DIR__."/User.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Supertest.php";
require_once __DIR__."/Render.php";
require_once __DIR__."/Tasks_block_constructor.php";


class Supertest_block_constructor extends Tasks_block_constructor
{
    public function get_supertest_block($user_id, $theme_id, $sptest_id, $is_admin=false)
    {
        $user = new User($user_id);
        // беру лимит баллов темы
        $theme = new Theme($theme_id);
        $theme->get_points_limit();

        // проверка доступа к супертесту
        $professor = new Professor();
        $users_progress = $professor->get_progress_theme($user, $theme);
        $resp = $professor->check_access_supertest($theme->points_limit, $users_progress, $is_admin);
        if(!$resp["status"])
            return ["block"=>$resp["error"]];

        $supertest = new Supertest($sptest_id);
        $supertests_tasks = $supertest->get_tasks();

        // отображение задач супертеста
        $render = new Render();
        $supertest_block = "";
        if ($is_admin)
            $supertest_block .= "<div class='col-12 d-flex justify-content-center mb-5'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$supertest->id'>Добавить задачу в супертест</a></div>";
        if ($supertests_tasks)
        {
            $supertest_block .=
                "<form class='col-12 send_supertest_answers'  method='POST' onsubmit='send_supertest_answers();return false;'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='send_supertest_answers'>
                                    <input type='hidden' name='theme_id' value='$theme->id'>";
            foreach ($supertests_tasks as $task)
            {
                $supertest_block .= $render->render_supertest_task($task);
                if ($is_admin)
                {
                    $supertest_block .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task->id&from_supertest=true&theme_id=$theme->id'>Изменить задачу</a></div>";
                    $supertest_block .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task->id);return false;'>Удалить эту задачу</button>
                                           </div>";
                }
            }
        }
        $supertest_block .= "<hr><div class='m-0 col-12 mt-5 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>";
        $supertest_block .= "<div class='col-12 mt-3 d-flex justify-content-center h2' id='message'></div>";
        $supertest_block .= "</form>";
        return ["block"=>$supertest_block];
    }
}
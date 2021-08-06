<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Theme.php";

require_once __DIR__."/Professor.php";
require_once __DIR__."/Professor_tasks.php";
require_once __DIR__."/Professor_mistakes.php";

require_once __DIR__."/Timer.php";

require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_mistakes_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/Themes_limits_table.php";
require_once __DIR__."/Themes_points_limit_table.php";
require_once __DIR__."/Users_themes_time_table.php";
session_start();


class Task_handler
{
    public $data, $task;

    public function send_answer()
    {
        $this->construct_task();
        $task = &$this->task;
        $user = &$this->data["user"];
        $theme = new Theme($task->theme_id);

        // Если задача в РО, отклоняю решение
        $prof_mist = new Professor_mistakes();
        $in_mistakes = $prof_mist->check_in_mistakes_list($user, $task);
        if($in_mistakes)
            return ["status" => "ERROR", "code"=>"IN_MISTAKES"];

        // Проверяю время
        $timer = new Timer();
        $response = $timer->check_time($user, $theme);
        if($response["status"]===false)
            return ["status" => "ERROR", "code"=>"TIME"];
        else if ($response["status"]==="update")
            $timer->set_theme_begin_time($user, $theme);

        $prof = new Professor_tasks();
        $status = $prof->check_task($task);
        if($status)
        {
            $status = $prof->add_task_to_users_tasks($user, $task);

            if($status) // если решается впервые добавляю балл
                $prof->add_point($user, $task);
            // это нужно в js для открытия супертеста
            $progress = $prof->get_points($user, $theme);
            $theme->get_points_limit();
            //
            return ["status" => "OK", "task_id"=>$task->id, "points_limit"=>$theme->points_limit, "progress"=>$progress];
        }
        else
        {
            $resp = ["status" => "ERROR"];
            $users_tasks = $prof->get_tasks($user);

            if(!in_array(["user_id"=>$user->id, "task_id"=>$task->id], $users_tasks)) // если пользователь эту задачу еще не решал
            {
                $prof_mist->add_to_mistakes($user, $task); // добавляю задачу в РО
                $prof->delete_point($user, $task); // снимаю балл
                $resp["task_id"] = $task->id;
            }
            return $resp;
        }

    }

    public function construct_task()
    {

        $this->task = new Task($this->data["task_id"]);
        $task = &$this->task;

        if($task->type == "A")
        {
            $task->get_A_answer();

            $task->users_answer = [];
            for($i=1; $i<=5; $i++)
            {
                if (isset($this->data[$task->id."_a_answ$i"]))
                    $task->users_answer[] = $i;
            }
        }
        else
            $task->users_answer = $this->data[$task->id."_b_answer"];
        return $task;
    }
}
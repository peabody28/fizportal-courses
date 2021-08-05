<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Users_mistakes_table.php";


class Mistake_handler extends Task_handler
{
    public function send_answer()
    {
        $task = $this->construct_task();
        $user = &$this->data["user"];

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $prof_mist = new Professor_mistakes();
            $prof_mist->add_task_to_users_tasks($user, $task);
            // добавляю балл в тему
            $prof_mist->add_point($user, $task);
            // удаляю из РО
            $prof_mist->delete_from_mistakes($user, $task);

            return ["status" => "OK", "task_id"=>$this->data["task_id"]];
        }
        else
            return ["status" => "ERROR", "task_id"=>$this->data["task_id"]];
    }
}
<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Task_handler.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Professor_mistakes.php";


class Mistake_handler extends Task_handler
{
    public function send_answer()
    {
        // TODO проверить этот метод
        $task = $this->construct_task();
        $user = &$this->data["user"];

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $prof->add_task_to_users_tasks($user, $task);
            // добавляю балл в тему
            $prof->add_point($user, $task);
            // удаляю из РО
            $prof_mist = new Professor_mistakes();
            $prof_mist->delete_from_mistakes($user, $task);

            return ["status" => "OK", "task_id"=>$task->id];
        }
        else
            return ["status" => "ERROR", "task_id"=>$task->id];
    }
}
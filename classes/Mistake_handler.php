<?php

class Mistake_handler extends Task_handler
{
    public function send_answer()
    {
        $task = $this->construct_task();

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $users_tasks_table = new Users_tasks_table();
            $users_tasks_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);
            // добавляю балл НУЖНО ЛИ? ТЕМА И ТАК ВЫПОЛНЕНА НА ЭТОМ ЭТАПЕ
            $users_progress_theme_table = new Users_progress_theme_table();
            $users_progress_theme_table->add_point(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]], (int)$task["complexity"]*2);
            // удаляю из РО
            $users_mistakes_table = new Users_mistakes_table();
            $users_mistakes_table->delete(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);

            return ["status" => "OK", "task_id"=>$this->data["task_id"]];
        }
        else
            return ["status" => "ERROR", "task_id"=>$this->data["task_id"]];
    }
}
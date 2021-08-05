<?php

class Professor_tasks extends Professor
{
    public function check_task($task)
    {
        return parent::check_task($task);
    }

    public function get_tasks($user)
    {
        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($user->id);
        return $users_tasks;
    }

    public function add_task_to_users_tasks($user, $task)
    {
        //добавление задачи в список решенных
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->create(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }

    public function delete_task_from_users_tasks($user, $task)
    {
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->delete(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
}
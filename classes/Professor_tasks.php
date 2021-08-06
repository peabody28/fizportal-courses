<?php
require_once __DIR__."/Professor.php";
require_once __DIR__."/Professor_mistakes.php";
require_once __DIR__."/Users_tasks_table.php";


class Professor_tasks extends Professor
{

    public function task_status($user, $task)
    {
        // "solved"
        // "open"
        // "close"

        $user_tasks = $this->get_tasks($user);

        $prof_mist = new Professor_mistakes();
        $user_mistakes = $prof_mist->get_mistakes($user);

        foreach ($user_tasks as $ut)
        {
            if($ut->id == $task->id)
                return "solved";
        }
        foreach ($user_mistakes as $um)
        {
            if($um->id == $task->id)
                return "close";
        }
        return "open";

    }

    public function get_tasks($user)
    {
        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($user->id);
        return $users_tasks;
    }

    public function delete_task_from_users_tasks($user, $task)
    {
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->delete(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
}
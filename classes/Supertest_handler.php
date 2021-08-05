<?php
require_once __DIR__."/Professor.php";//
require_once __DIR__."/Users_themes_table.php";//


class Supertest_handler extends Task_handler
{
    public function send_answer()
    {
        $str = "";
        foreach ($this->data as $key => $val)
        {
            if($key=="code" || $key == "submit")
                continue;
            $str .= "&".$key."=";
        }
        $match = [];
        preg_match_all("/&([0-9]*)_{1}a{0,1}b{0,1}_{1}/u", $str, $match);

        $tasks = [];

        foreach (array_unique($match[1]) as $task_id)
        {
            $this->data["task_id"] = $task_id;
            $task = $this->construct_task();
            array_push($tasks, $task);
        }

        // проверка
        $status = true;
        $prof_task = new Professor_tasks();
        foreach ($tasks as $item)
        {
            $status = $prof_task->check_task($item);
            if(!$status)
                break;
        }
        if ($status)
        {
            $users_themes_table = new Users_themes_table();
            $users_themes_table->create(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
            return ["status"=>"OK"];
        }
        else
            return ["status"=>"ERROR"];

    }
}
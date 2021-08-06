<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Users_mistakes_table.php";

class Professor_mistakes extends Professor
{

    public function get_mistakes($user)
    {
        $list = [];
        $users_mistakes_table = new Users_mistakes_table();
        $users_mistakes = $users_mistakes_table->read($user->id);
        foreach ($users_mistakes as $item)
        {
            $task = new Task($item["task_id"]);
            $list[] = $task;
        }
        return $list;
    }

    public function add_to_mistakes($user, $task)
    {
        $users_mistakes_table = new Users_mistakes_table();
        $status = $users_mistakes_table->create(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }

    public function delete_from_mistakes($user, $task)
    {
        $users_mistakes_table = new Users_mistakes_table();
        $status = $users_mistakes_table->delete(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }

    public function check_in_mistakes_list($user, $task)
    {
        $mist_list = $this->get_mistakes($user);
        return in_array(["user_id"=>$user->id, "task_id"=>$task->id], $mist_list);
    }

    public function mistakes_status($theme_id)
    {

        $themes_table = new Themes_table();
        $theme = $themes_table->read($theme_id); // тема для которой делается РО

        $themes_list_full = $themes_table->get_courses_themes($theme["course_id"]);

        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($_SESSION["id"]);

        $themes_ids = [];
        foreach ($themes_list_full as $item)
            $themes_ids[] = $item["id"];

        $tec_local_id = array_search($theme_id, $themes_ids);
        $sled_id = $themes_list_full[(int)$tec_local_id+1]["id"];
        if(in_array(["user_id"=>$_SESSION["id"], "theme_id"=>$sled_id], $users_themes_list))
            return true;
        else
            return false;

    }

    public function add_point($user, $task)
    {
        // переопределяю чтоб начислялось х2 баллов
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->add_point(["user_id"=>$user->id, "theme_id"=>$task->theme_id], (int)($task->complexity)*2);
    }

}
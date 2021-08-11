<?php

require_once __DIR__."/Course.php";
require_once __DIR__."/Task.php";
require_once __DIR__."/Mistake.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Users_mistakes_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Themes_table.php";

class Professor_mistakes extends Professor
{

    public function get_mistakes($user)
    {
        $list = [];
        $users_mistakes_table = new Users_mistakes_table();
        $users_mistakes = $users_mistakes_table->read($user->id);
        foreach ($users_mistakes as $item)
        {
            $task = new Mistake($item["task_id"]);
            $list[] = $task;
        }
        return $list;
    }

    public function get_mistakes_for_theme($user, $theme)
    {
        $all_mistakes = $this->get_mistakes($user);

        $tasks_theme = $theme->get_tasks();
        $tasks_theme_ids = [];
        foreach ($tasks_theme as $tt)
            $tasks_theme_ids[] = $tt->id;

        $mistakes = [];
        foreach ($all_mistakes as $mistake)
        {
            if(in_array($mistake->id, $tasks_theme_ids))
                $mistakes[] = $mistake;
        }
        return $mistakes;
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
        foreach ($mist_list as $item)
        {
            if($item->id == $task->id)
                return true;
        }
        return false;
    }

    public function mistakes_status($user, $theme)
    {
        // курс в котором эта тема
        $course = new Course($theme->course_id);
        // все темы этого курса
        $courses_themes = $course->get_themes();
        // их id
        $courses_themes_ids = $course->get_themes_ids();

        // темы, выполненные пользователем
        $professor = new Professor();
        $users_themes = $professor->get_themes($user);

        // вычисляю id темы следующей за этой
        $theme_number_in_course = array_search($theme->id, $courses_themes_ids);
        $next_theme_id = isset($courses_themes[$theme_number_in_course+1])?$courses_themes[$theme_number_in_course+1]->id:null;

        // если пользователь выполнил ее - даю доступ к РО
        foreach ($users_themes as $u_th)
        {
            if($u_th->id == $next_theme_id)
                return true;
        }
        return false;

    }

    public function add_point($user, $task)
    {
        // переопределяю чтоб начислялось х2 баллов
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->add_point(["user_id"=>$user->id, "theme_id"=>$task->theme_id], (int)($task->complexity)*2);
    }

}
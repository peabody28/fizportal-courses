<?php
require_once __DIR__."/Course.php";
require_once __DIR__."/Professor_mistakes.php";

require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_themes_time_table.php";
require_once __DIR__."/Themes_limits_table.php";
require_once __DIR__."/Users_progress_theme_table.php";


class Professor
{
    protected function check_task($task)
    {
        $status = true;

        if ($task->type == "A") {
            for($i=1; $i<=5;$i++)
            {
                if( (!in_array(["task_id"=>$task->id, "answer"=>$i], $task->answer) && in_array($i, $task->users_answer) ) ||
                    (in_array(["task_id"=>$task->id, "answer"=>$i], $task->answer) && !in_array($i, $task->users_answer)) )
                {
                    $status=false;
                    break;
                }
            }
        }
        else
            $status = ($task->answer == $task->users_answer);

        return $status;
    }

    public function get_themes($user)
    {
        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($user->id);
        return $users_themes_list;
    }
    public function get_progress_theme($user, $theme)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        return (int)$users_progress["progress"];
    }
    public function set_progress_theme($user, $theme, $progress)
    {
        $row = ["user_id"=>$user->id, "theme_id"=>$theme->id, "progress"=>$progress];
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->update($row, "set_point");
    }
    public function theme_status($user, $theme)
    {
        // список тем курса
        $course = new Course($theme->course_id);
        $themes_ids = $course->get_themes_ids();
        // список тем решенных пользователем
        $users_themes_list = $this->get_themes($user);

        if (in_array(["user_id" => $user->id, "theme_id" => $theme->id], $users_themes_list))
            return "solved";
        else if ($theme->id==$themes_ids[0]) // первая тема курса
            return "open";
        else if ($theme->id==$themes_ids[1])// вторая тема курса
        {
            if (in_array(["user_id" => $user->id, "theme_id" => $themes_ids[0]], $users_themes_list))
                return "open";
            else
                return "close";
        }
        else // >=3
        {
            $pred_id = array_search($theme->id, $themes_ids)-1;
            if (in_array(["user_id" => $user->id, "theme_id" => $themes_ids[$pred_id]], $users_themes_list)) // предыдущая решена?
            {
                $theme = new Theme($themes_ids[$pred_id-1]);
                $tasks_ids = $theme->get_tasks_ids();
                $prof_mist = new Professor_mistakes();
                $mistakes = $prof_mist->get_mistakes($user);
                foreach ($mistakes as $mistake)
                {
                    if (in_array($mistake["task_id"], $tasks_ids))
                        return "close";
                }
                return "open";
            }
            else
                return "close";
        }

    }
    public function reset_theme($user, $theme)
    {

        $tasks_theme_list = $theme->get_tasks();
        $prof_tasks = new Professor_tasks();
        $users_tasks = $prof_tasks->get_tasks($user);

        $prof_mist = new Professor_mistakes();
        $users_mistakes = $prof_mist->get_mistakes($user);

        $users_themes = $this->get_themes($user);

        $progress = 0;
        foreach ($tasks_theme_list as $task)
        {
            // если задача в работе над ошибками - удаляю
            $obj = ["user_id"=>$user->id, "task_id"=>$task->id];
            if(in_array($obj, $users_mistakes))
            {
                if (!in_array(["user_id"=>$user->id, "theme_id"=>$theme->id], $users_themes))
                    $prof_mist->delete_from_mistakes($user, $task);
                else
                    $progress--;
            }
            // если задача в списке решенных - удаляю
            else if(in_array($obj, $users_tasks))
                $prof_tasks->delete_task_from_users_tasks($user, $task);
        }
        $this->set_progress_theme($user, $theme, $progress);
        // обнуляю время
        $this->delete_theme_begin_time($user, $theme);

        return ["status"=>"OK"];
    }
    public function get_theme_begin_time($user, $theme)
    {
        $users_themes_time = new Users_themes_time_table();
        $resp = $users_themes_time->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        $time = (int)$resp["time"];
        return $time;
    }

    public function check_time($user, $theme)
    {

        $users_themes_list = $this->get_themes($user);

        if(in_array(["user_id"=>$user->id, "theme_id"=>$theme->id], $users_themes_list) || $user->rights == "admin") // пользователь уже сделал тему
            return ["status"=>true, "theme_is_solved"=>true];
        // узнаю лимит выполнения темы
        $theme->get_time_limit();
        if(!$theme->time_limit)
            return ["status"=>true];

        $time = $this->get_theme_begin_time($user, $theme);
        if ($time)
        {
            $real_time = time();
            $delta = $real_time - (int)$time;

            if($delta <= $theme->time_limit*60) // если разница во времени меньше времени на тему(30м) - пропускаем
            {
                $hours = (int)(($theme->time_limit*60-$delta)/3600);
                $min = (int)(($theme->time_limit*60-$delta)/60)-$hours*60;
                $sec = ($theme->time_limit*60-$delta)-$hours*3600-$min*60;
                return ["status"=>true, "theme_is_solved"=>false, "hours"=>$hours, "min"=>$min, "sec"=>$sec];
            }
            else if($delta > $theme->time_limit*60 && $delta < $theme->time_limit*60*2+$theme->time_limit*60) // если разница во времени больше времени на тему и меньше штрафа+время на тему (5ч+30м) - запрет на решение
            {
                $hours = (int)(($theme->time_limit*2*60+$theme->time_limit*60 - $delta)/3600);
                $min = (int)(($theme->time_limit*2*60+$theme->time_limit*60 - $delta)/60) - $hours*60;
                $sec = (int)(($theme->time_limit*2*60+$theme->time_limit*60 - $delta)) - $hours*3600 - $min*60;
                return ["status"=>false, "theme_is_solved"=>false, "hours"=>$hours, "min"=>$min, "sec"=>$sec];
            }

            else // если разница во времени больше штрафа+время на тему - пропускаем и записываем новое время в таблицу
                return ["status"=>"update", "theme_is_solved"=>false, "hours"=>(int)($theme->time_limit/60), "min"=>$theme->time_limit-((int)($theme->time_limit/60))*60, "sec"=>0];
        }
        else
            return ["status"=>"update", "theme_is_solved"=>false, "hours"=>(int)($theme->time_limit/60), "min"=>$theme->time_limit-((int)($theme->time_limit/60))*60, "sec"=>0];
    }
    public function set_theme_begin_time($user, $theme)
    {
        $row = ["user_id"=>$user->id, "theme_id"=>$theme->id];
        $users_themes_time = new Users_themes_time_table();
        $row["time"]=time();
        $users_themes_time->update($row, "time");
    }
    public function delete_theme_begin_time($user, $theme)
    {
        $users_themes_time_table = new Users_themes_time_table();
        $users_themes_time_table->delete(["user_id"=>$user->id, "theme_id"=>$theme->id]);
    }


    public function check_access_supertest($limits_of_points, $users_progress, $is_admin=false)
    {
        if((int)$users_progress["progress"]<(int)$limits_of_points && !$is_admin)
        {
            $progress = $users_progress["progress"]?:"0";
            return ["status"=>false ,"error" => "Вы решили мало задач ваш балл ".$progress."/".$limits_of_points];
        }
        return ["status"=>true];
    }

    public function get_points($user, $theme)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $resp = $users_progress_theme_table->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        return (int)( $resp?$resp["progress"]:0 );
    }
    public function add_point($user, $task)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->add_point(["user_id"=>$user->id, "theme_id"=>$task->theme_id], $task->complexity);
    }
    public function delete_point($user, $task)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->delete_point(["user_id" => $user->id, "theme_id" => $task->theme_id], $task->complexity);
    }



}
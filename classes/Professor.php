<?php
require_once __DIR__."/Course.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Timer.php";
require_once __DIR__."/Professor_mistakes.php";

require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_courses_table.php";
require_once __DIR__."/Users_themes_time_table.php";
require_once __DIR__."/Themes_limits_table.php";
require_once __DIR__."/Users_progress_theme_table.php";


class Professor
{
    public function check_task($task)
    {
        // TODO проверить этот метод
        $status = true;

        if ($task->type == "A") {
            for($i=1; $i<=5;$i++)
            {
                if( (!in_array($i, $task->answer) && in_array($i, $task->users_answer) ) ||
                    (in_array($i, $task->answer) && !in_array($i, $task->users_answer)) )
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

    public function add_theme_to_users_themes($user, $theme)
    {
        $users_themes_table = new Users_themes_table();
        $users_themes_table->create(["user_id"=>$user->id, "theme_id"=>$theme->id]);
    }
    public function add_task_to_users_tasks($user, $task)
    {
        //добавление задачи в список решенных
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->create(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }

    public function get_themes($user)
    {
        $themes = [];
        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($user->id);
        foreach ($users_themes_list as $item)
        {
            $theme = new Theme($item["theme_id"]);
            $themes[]=$theme;
        }

        return $themes;
    }
    public function get_progress_theme($user, $theme)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        return (int)(isset($users_progress["progress"]) ?? 0);
    }
    public function set_progress_theme($user, $theme, $progress)
    {
        $row = ["user_id"=>$user->id, "theme_id"=>$theme->id, "progress"=>$progress];
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->update($row, "set_point");
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
                    $progress -= $task->complexity;
            }
            // если задача в списке решенных - удаляю
            else if(in_array($obj, $users_tasks))
                $prof_tasks->delete_task_from_users_tasks($user, $task);
        }
        // обновляю прогресс
        $this->set_progress_theme($user, $theme, $progress);
        // обнуляю время
        $timer = new Timer();
        $timer->delete_theme_begin_time($user, $theme);

        return ["status"=>"OK"];
    }

    public function theme_status($user, $theme)
    {
        // список id тем курса
        $course = new Course($theme->course_id);
        $themes_ids = $course->get_themes_ids();
        // список тем решенных пользователем
        $users_themes_list = $this->get_themes($user);

        $users_themes_ids_list = [];
        foreach ($users_themes_list as $th)
            $users_themes_ids_list[] = $th->id;

        if(in_array($theme->id, $users_themes_ids_list))
            return ["status"=>"solved"]; // тема в списке решенных
        else if ($theme->id == $themes_ids[0]) // первая тема курса
            return ["status"=>"open"];
        else if ($theme->id == $themes_ids[1])// вторая тема курса
        {
            if (in_array($themes_ids[0], $users_themes_ids_list)) // если первая решена - открываю вторую
                return ["status"=>"open"];
            else
                return ["status"=>"close", "message"=>"Вы не решили первую тему"];
        }
        else // >=3
        {
            $pred_id = array_search($theme->id, $themes_ids)-1;
            if (in_array($themes_ids[$pred_id], $users_themes_ids_list)) // предыдущая решена?
            {
                // проверяю РО темы с индексом -2 относительно данной
                $tmp_theme = new Theme($themes_ids[$pred_id-1]);
                $tasks_ids = $tmp_theme->get_tasks_ids();
                $prof_mist = new Professor_mistakes();
                $mistakes = $prof_mist->get_mistakes($user);
                foreach ($mistakes as $mistake)
                {
                    if (in_array($mistake->id, $tasks_ids))
                        return ["status"=>"close", "message"=>"Вы не сделали работу над ошибками одной из предыдущих тем, зайдите в Личный кабинет"];
                }
                return ["status"=>"open"];
            }
            else
                return ["status"=>"close", "message"=>"Вы не решили предыдущую тему"];
        }

    }
    public function check_access_supertest($user, $theme)
    {
        $users_progress = $this->get_progress_theme($user, $theme);
        $theme->get_points_limit();

        if($users_progress < $theme->points_limit && !($user->rights == "admin"))
            return ["status"=>false ,"error" => "Вы решили мало задач ваш балл ".$users_progress."/".$theme->points_limit];
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
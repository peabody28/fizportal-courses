<?php
require_once __DIR__."/Course.php";
require_once __DIR__ . "/Task.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Mistake.php";
require_once __DIR__."/Timer.php";

require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Users_mistakes_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_courses_table.php";
require_once __DIR__."/Users_themes_time_table.php";
require_once __DIR__."/Themes_limits_table.php";
require_once __DIR__."/Users_progress_theme_table.php";


class Professor
{
    public $student;

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
    public function task_status($task)
    {
        $user = &$this->student;
        // "solved"
        // "open"
        // "close"
        if($user->rights == "admin")
            return "open";
        $user_tasks = $this->get_tasks($user);
        $user_mistakes = $this->get_mistakes($user);

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
    public function get_tasks()
    {
        $user = &$this->student;
        $list = [];
        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($user->id);
        foreach ($users_tasks as $ut)
        {
            $task = new Task($ut["task_id"]);
            $list[] = $task;
        }

        return $list;
    }
    public function delete_task_from_users_tasks($task)
    {
        $user = &$this->student;
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->delete(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
    public function add_task_to_users_tasks($task)
    {
        $user = &$this->student;
        //добавление задачи в список решенных
        $users_tasks_table = new Users_tasks_table();
        $status = $users_tasks_table->create(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
    public function in_users_tasks($task)
    {
        $user = &$this->student;
        $users_tasks = $this->get_tasks($user);
        foreach ($users_tasks as $u_task)
        {
            if ($u_task->id == $task->id)
                return 1;
        }
        return 0;
    }


    public function get_mistakes()
    {
        $user = &$this->student;
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
    public function get_mistakes_for_theme($theme)
    {
        $user = &$this->student;
        $all_mistakes = $this->get_mistakes();

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
    public function add_to_mistakes($task)
    {
        $user = &$this->student;
        $users_mistakes_table = new Users_mistakes_table();
        $status = $users_mistakes_table->create(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
    public function delete_from_mistakes($task)
    {
        $user = &$this->student;
        $users_mistakes_table = new Users_mistakes_table();
        $status = $users_mistakes_table->delete(["user_id"=>$user->id, "task_id"=>$task->id]);
        return $status;
    }
    public function check_in_mistakes_list($task)
    {
        $user = &$this->student;
        $mist_list = $this->get_mistakes($user);
        foreach ($mist_list as $item)
        {
            if($item->id == $task->id)
                return true;
        }
        return false;
    }
    public function mistakes_status($theme)
    {
        // курс в котором эта тема
        $course = new Course($theme->course_id);
        // все темы этого курса
        $courses_themes = $course->get_themes();
        // их id
        $courses_themes_ids = $course->get_themes_ids();
        // темы, выполненные пользователем
        $users_themes = $this->get_themes();

        // если одна из двух след. тем выполнена - разрешаю доступ
        $theme_number_in_course = array_search($theme->id, $courses_themes_ids);

        if($theme_number_in_course+1 < count($courses_themes) )
        {
            foreach ($users_themes as $u_th)
            {
                if($u_th->id == $courses_themes_ids[$theme_number_in_course+1] || $theme_number_in_course+2 < count($courses_themes) &&  $u_th->id == $courses_themes_ids[$theme_number_in_course+2])
                    return true;
            }
        }

        return false;

    }


    public function theme_status($theme)
    {
        $user = &$this->student;
        if($user->rights == "admin")
            return ["status"=>"open"];
        // список id тем курса
        $course = new Course($theme->course_id);
        $themes_ids = $course->get_themes_ids();
        // список тем решенных пользователем
        $users_themes_list = $this->get_themes();

        $users_themes_ids_list = [];
        $mistakes_count = 0;
        foreach ($users_themes_list as $th)
        {
            $users_themes_ids_list[] = $th->id;
            $mistakes_count += count($this->get_mistakes_for_theme($th));
        }

        //Если ошибок больше 10 - закрываю все темы
        if(in_array($theme->id, $users_themes_ids_list))
            return ["status"=>"solved", "mist"=>($mistakes_count >= 10)]; // тема в списке решенных
        else if ($theme->id == $themes_ids[0]) // первая тема курса
            return ["status"=>"open", "mist"=>($mistakes_count >= 10)];
        else if ($theme->id == $themes_ids[1])// вторая тема курса
        {
            if (in_array($themes_ids[0], $users_themes_ids_list)) // если первая решена - открываю вторую
                return ["status"=>"open", "mist"=>($mistakes_count >= 10)];
            else
                return ["status"=>"close", "message"=>"Вы не решили первую тему"];
        }
        else // >=3
        {
            $this_id = array_search($theme->id, $themes_ids);
            // TODO статус темы не зависит от РО
            if (in_array($themes_ids[$this_id-1], $users_themes_ids_list) || in_array($themes_ids[$this_id-2], $users_themes_ids_list)) // предыдущая решена?
                return ["status"=>"open", "mist"=>($mistakes_count >= 10)];
            else
                return ["status"=>"close", "message"=>"Вы пока не можете решать эту тему"];
        }

    }

    public function add_theme_to_users_themes($theme)
    {
        $user = &$this->student;
        $users_themes_table = new Users_themes_table();
        $users_themes_table->create(["user_id"=>$user->id, "theme_id"=>$theme->id]);
    }
    public function get_themes()
    {
        $user = &$this->student;
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
    public function get_progress_theme($theme)
    {
        $user = &$this->student;
        $users_progress_theme_table = new Users_progress_theme_table();
        $user_progress = $users_progress_theme_table->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        $user_progress = $user_progress["progress"] ? : 0;
        return (int)$user_progress;
    }
    public function set_progress_theme($theme, $progress)
    {
        $user = &$this->student;
        $row = ["user_id"=>$user->id, "theme_id"=>$theme->id, "progress"=>$progress];
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->update($row, "set_point");
    }
    public function reset_theme($theme)
    {
        $user = &$this->student;
        $tasks_theme_list = $theme->get_tasks();

        $progress = 0;
        foreach ($tasks_theme_list as $task)
        {
            if ($this->in_users_tasks($task))
            {
                $this->delete_task_from_users_tasks($task);
                continue;
            }
            if ($this->check_in_mistakes_list($task))
            {
                $theme_status = $this->theme_status($theme);
                $theme_status = $theme_status["status"];
                if ($theme_status == "open")
                    $this->delete_from_mistakes($task);
                else
                    $progress -= $task->complexity;
            }

        }
        // обновляю прогресс
        $this->set_progress_theme($theme, $progress);
        // обнуляю время
        $timer = new Timer();
        $timer->delete_theme_begin_time($user, $theme);

        return ["status"=>"OK"];
    }


    public function check_access_supertest($theme)
    {
        $user = &$this->student;
        if($user->rights == "admin")
            return ["status"=>true];

        $users_progress = $this->get_progress_theme($theme);
        $theme->get_points_limit();

        if($users_progress < $theme->points_limit)
            return ["status"=>false ,"error" => "Вы решили мало задач ваш балл ".$users_progress."/".$theme->points_limit];
        return ["status"=>true];
    }


    public function get_points($theme)
    {
        $user = &$this->student;
        $users_progress_theme_table = new Users_progress_theme_table();
        $resp = $users_progress_theme_table->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        return (int)( $resp?$resp["progress"]:0 );
    }
    public function add_point($task)
    {
        $user = &$this->student;
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->add_point(["user_id"=>$user->id, "theme_id"=>$task->theme_id], $task->complexity);
    }
    public function delete_point($task)
    {
        $user = &$this->student;
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress_theme_table->delete_point(["user_id" => $user->id, "theme_id" => $task->theme_id], $task->complexity);
    }


    public function check_time($theme)
    {
        $user = &$this->student;
        $timer = new Timer();
        return $timer->check_time($user, $theme);
    }
}

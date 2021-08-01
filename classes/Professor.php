<?php
require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Users_mistakes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__ . "/Users_themes_time_table.php";
require_once __DIR__."/Themes_limits_table.php";

class Professor
{
    public function check_task($task)
    {
        $status = true;

        if ($task["type"]=="A") {
            for($i=1; $i<=5;$i++)
            {
                if( (!in_array(["task_id"=>$task["id"], "answer"=>$i], $task["answers"]) && in_array($i, $task["user_answers"]) ) ||
                    (in_array(["task_id"=>$task["id"], "answer"=>$i], $task["answers"]) && !in_array($i, $task["user_answers"])) )
                {
                    $status=false;
                    break;
                }
            }
        }
        else
            $status = ($task["answer"]==$task["user_answer"]);

        return $status;
    }

    public function theme_status($theme)
    {
        // список тем курса
        $themes_table = new Themes_table();
        $themes_list_full = $themes_table->get_courses_themes($theme["course_id"]);
        $themes_ids = [];
        foreach ($themes_list_full as $item)
            $themes_ids[] = $item["id"];

        // список тем решенных пользователем
        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($_SESSION["id"]);

        if (in_array(["user_id" => $_SESSION["id"], "theme_id" => $theme["id"]], $users_themes_list))
            return "solved";
        else if ($theme["id"]==$themes_ids[0]) // первая тема курса
            return "open";
        else if ($theme["id"]==$themes_ids[1])// вторая тема курса
        {
            if (in_array(["user_id" => $_SESSION["id"], "theme_id" => $themes_ids[0]], $users_themes_list))
                return "open";
            else
                return "close";
        }
        else // >=3
        {
            $pred_id = array_search($theme["id"], $themes_ids)-1;
            if (in_array(["user_id" => $_SESSION["id"], "theme_id" => $themes_ids[$pred_id]], $users_themes_list)) // предыдущая решена?
            {
                $tasks_table = new Tasks_table();
                $tasks_theme_full = $tasks_table->get_tasks_theme($themes_ids[$pred_id-1]);
                $tasks_ids = [];
                foreach ($tasks_theme_full as $task)
                    $tasks_ids[] = $task["id"];

                $users_mistakes_table = new Users_mistakes_table();
                $mistakes = $users_mistakes_table->read($_SESSION["id"]); // работа над ошибками пользователя
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

    public function check_time($row)
    {

        $users_themes_table = new Users_themes_table();
        $users_themes_list= $users_themes_table->read($_SESSION["id"]);
        if(in_array(["user_id"=>$_SESSION["id"], "theme_id"=>$row["theme_id"]], $users_themes_list) || $_SESSION["rights"]=="admin") // пользователь уже сделал тему
            return ["status"=>true, "theme_is_solved"=>true];
        // узнаю лимит выполнения темы
        $themes_limits_table = new Themes_limits_table();
        $answ = $themes_limits_table->read($row["theme_id"]);
        $limit = $answ?(int)$answ["time_limit"]:null;
        if(!$limit)
            return true;
        $users_themes_time = new Users_themes_time_table();
        $resp = $users_themes_time->read($row);
        $time = $resp["time"];
        if ($time)
        {
            $real_time = time();
            $delta = $real_time - (int)$time;

            if($delta <= $limit*60) // если разница во времени меньше времени на тему(30м) - пропускаем
            {
                $hours = (int)(($limit*60-$delta)/3600);
                $min = (int)(($limit*60-$delta)/60)-$hours*60;
                $sec = ($limit*60-$delta)-$hours*3600-$min*60;
                return ["status"=>true, "theme_is_solved"=>false, "hours"=>$hours, "min"=>$min, "sec"=>$sec];
            }
            else if($delta > $limit*60 && $delta < $limit*60*2+$limit*60) // если разница во времени больше времени на тему и меньше штрафа+время на тему (5ч+30м) - запрет на решение
            {
                $hours = (int)(($limit*2*60+$limit*60 - $delta)/3600);
                $min = (int)(($limit*2*60+$limit*60 - $delta)/60) - $hours*60;
                $sec = (int)(($limit*2*60+$limit*60 - $delta)) - $hours*3600 - $min*60;
                return ["status"=>false, "theme_is_solved"=>false, "hours"=>$hours, "min"=>$min, "sec"=>$sec];
            }

            else // если разница во времени больше штрафа+время на тему - пропускаем и записываем новое время в таблицу
                return ["status"=>"update", "theme_is_solved"=>false, "hours"=>(int)($limit/60), "min"=>$limit-((int)($limit/60))*60, "sec"=>0];
        }
        else
            return ["status"=>"update", "theme_is_solved"=>false, "hours"=>(int)($limit/60), "min"=>$limit-((int)($limit/60))*60, "sec"=>0];
    }

    public function set_time($row)
    {
        $users_themes_time = new Users_themes_time_table();
        $row["time"]=time();
        $users_themes_time->update($row, "time");
    }

    public function check_access_supertest($users_progress, $is_admin=false)
    {
        if((int)$users_progress["progress"]<10 && !$is_admin)
        {
            $progress = $users_progress["progress"]?:"0";
            return ["status"=>false ,"error" => "Вы решили мало задач ваш балл ".$progress."/10"];
        }
        return ["status"=>true];
    }


}
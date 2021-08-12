<?php
require_once __DIR__."/Professor.php";
require_once __DIR__."/Users_themes_time_table.php";


class Timer
{
    public function check_time($user, $theme)
    {
        if ($user->rights == "admin")
            return ["status"=>true, "theme_is_solved"=>true];

        $prof = new Professor();
        $users_themes_list = $prof->get_themes($user);
        foreach($users_themes_list as $th)
        {
            if($theme->id == $th->id) // пользователь уже сделал тему
                return ["status"=>true, "theme_is_solved"=>true];
        }

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
                return ["status"=>true, "theme_is_solved"=>false, "sec"=>(int)($theme->time_limit*60-$delta)];
            else if($delta > $theme->time_limit*60 && $delta < $theme->time_limit*60*2+$theme->time_limit*60) // если разница во времени больше времени на тему и меньше штрафа+время на тему (5ч+30м) - запрет на решение
                return ["status"=>false, "theme_is_solved"=>false, "sec"=>(int)($theme->time_limit*2*60+$theme->time_limit*60 - $delta)];
            else // если разница во времени больше штрафа+время на тему - пропускаем и записываем новое время в таблицу
                return ["status"=>"update", "theme_is_solved"=>false, "sec"=>(int)$theme->time_limit];
        }
        else
            return ["status"=>"update", "theme_is_solved"=>false, "sec"=>(int)$theme->time_limit];
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

    public function get_theme_begin_time($user, $theme)
    {
        $users_themes_time = new Users_themes_time_table();
        $resp = $users_themes_time->read(["user_id"=>$user->id, "theme_id"=>$theme->id]);
        $time = (int)$resp["time"];
        return $time;
    }

    public function seconds_normalizations($seconds)
    {
        $seconds = (int)$seconds;
        $hours = $seconds/3600;
        $h_zero = ((int)($hours/10))?"":"0";
        $minutes = ($seconds%3600)/60;
        $m_zero = ((int)($minutes/10))?"":"0";
        $sec = $seconds%60;
        $s_zero = (int)($sec/10)?"":"0";
        return "<div id='hours'>$h_zero$hours</div>:<div id='min'>$m_zero$minutes</div>:<div id='sec'>$s_zero$sec</div>";
    }
}
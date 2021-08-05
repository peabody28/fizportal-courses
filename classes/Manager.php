<?php
require_once __DIR__."/Professor.php";

class Manager
{
    public function check_course($user, $course_id)
    {
        $prof = new Professor();
        $users_courses = $prof->get_courses($user);

        if($user->rights == "admin")
            return ["status"=>true];

        foreach ($users_courses as $item)
        {
                if ($course_id == $item->id)
                    return ["status"=>true];
        }
        return ["status"=>false, "error"=>"Вы не купили этот курс"];
    }
}
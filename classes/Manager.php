<?php
require_once __DIR__."/Course.php";
require_once __DIR__."/Users_courses_table.php";


class Manager
{
    public function check_course($user, $course_id)
    {
        $users_courses = $this->get_users_courses($user);

        if($user->rights == "admin")
            return ["status"=>true];

        foreach ($users_courses as $item)
        {
                if ($course_id == $item->id)
                    return ["status"=>true];
        }
        return ["status"=>false, "error"=>"Вы не купили этот курс"];
    }

    public function get_users_courses($user)
    {
        $list = [];
        $users_courses_table = new Users_courses_table();
        $users_courses = $users_courses_table->read($user->id);
        foreach ($users_courses as $item)
        {
            $course = new Course($item["course_id"]);
            $list[] = $course;
        }
        return $list;
    }
}
<?php
require_once __DIR__."/Course.php";
require_once __DIR__."/Courses_table.php";
require_once __DIR__."/Users_courses_table.php";


class Manager
{
    public function check_access_to_course($user, $course_id)
    {
        // получаю курсы пользователя
        $users_courses = $this->get_users_courses($user);

        if($user->rights == "admin")
            return ["status"=>true, "is_admin"=>true];

        foreach ($users_courses as $item)
        {
                if ($course_id == $item->id)
                    return ["status"=>true];
        }
        return ["status"=>false, "message"=>"Вы не купили этот курс"];
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

    public function get_courses()
    {
        $list = [];
        $courses_table = new Courses_table();
        $courses_list = $courses_table->get_courses_list();
        foreach ($courses_list as $item)
        {
            $course = new Course($item["course_id"]);
            $list[] = $course;
        }
        return $list;
    }

    public function buy_course($user, $course)
    {
        // обработка покупки
        $users_courses_table = new Users_courses_table();
        $status = $users_courses_table->create(["user_id"=>$user->id, "course_id"=>$course->id]);
        return ["status"=>$status, "course_id"=>$course->id];
    }
}
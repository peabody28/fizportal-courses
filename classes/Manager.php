<?php
require_once __DIR__."/Users_courses_table.php";

class Manager
{
    public function check_course($user_id, $course_id)
    {
        $users_courses_table = new Users_courses_table();
        $users_courses = $users_courses_table->read($user_id);
        if (in_array(["user_id" => $user_id, "course_id" => $course_id], $users_courses) || $_SESSION["rights"] == "admin")
            return ["status"=>true];
        return ["status"=>false, "error"=>"Вы не купили этот курс"];
    }
}
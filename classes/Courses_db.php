<?php
require_once __DIR__."/../db.php";

class Courses_db
{
    public function add(Course $course)
    {
        R::selectDatabase("courses_list");
        $row = R::dispense("courses");
        $row->name = $course->name;
        $row->title = $course->title;
        $course->id = R::store($row);
        R::selectDatabase("default");
        return $course->id?["status"=>"OK"]:["status"=>"ERROR", "error"=>"not work("];
    }
    public function get_course(Course $course)
    {
        R::selectDatabase("courses_list");
        $row =  R::findOne("courses", "id = ?", [$course->id]);
        R::selectDatabase("default");
        if($row)
        {
            $course->id = $row->id;
            $course->name = $row->name;
            $course->title = $row->title;
            $course->themes = $row->themes;
            $course->existence = true;
        }
        else
            $course->existence = null;
    }
    public function get_courses_list():array
    {
        R::selectDatabase("courses_list");
        $courses_list = R::findAll("courses");
        R::selectDatabase("default");
        return $courses_list;
    }
}
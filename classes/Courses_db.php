<?php
require_once __DIR__."/../db.php";

class Courses_db
{
    //курсы
    public function add(Course $course)
    {
        R::selectDatabase("courses_list");
        $row = R::dispense("courses");
        $row->name = $course->name;
        $row->title = $course->title;
        $row->themes = json_encode([]);
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
            $course->themes = json_decode($row->themes);
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
    public function delete(Course $course)
    {
        R::selectDatabase("courses_list");
        $row =  R::load("courses", $course->id);
        R::trash($row);
        R::selectDatabase("default");
    }
    public function update_themes(Course $course)
    {
        R::selectDatabase("courses_list");
        $row = R::load("courses", $course->id);
        $row->themes = json_encode($course->themes);
        R::store($row);
        R::selectDatabase("default");
    }
    //темы
    public function add_theme(Theme $theme)
    {
        R::selectDatabase("courses_list");
        $row = R::dispense("themes");
        $row->name = $theme->name;
        $theme->id = R::store($row);
        R::selectDatabase("default");
    }
    public function get_theme(Theme $theme)
    {
        R::selectDatabase("courses_list");
        $row = R::load("themes", $theme->id);
        R::selectDatabase("default");
        if($row)
        {
            $theme->name = $row->name;
            $theme->tasks = $row->tasks;
            $theme->level = $row->level;
            $theme->existence=true;
        }
    }

}
<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";


class Courses_table implements Table
{
    public function create($course)
    {
        R::selectDatabase("courses_list");
        $row = R::dispense("courses");
        $row->name = $course->name;
        $row->title = $course->title;
        $row->themes = json_encode($course->themes);
        $course->id = R::store($row);
        R::selectDatabase("default");
        return $course->id ? true: false;
    }
    public function read($course)
    {
        R::selectDatabase("courses_list");
        $row =  R::findOne("courses", "id = ?", [$course->id]);
        R::selectDatabase("default");
        return $row;
    }
    public function update($course, $code)
    {
        R::selectDatabase("courses_list");
        $row = R::load("courses", $course->id);
        if($code == "themes")
            $row->themes = json_encode($course->themes);
        R::store($row);
        R::selectDatabase("default");
    }
    public function delete($course)
    {
        R::selectDatabase("courses_list");
        $row =  R::load("courses", $course->id);
        R::trash($row);
        R::selectDatabase("default");
    }

    public function get_courses_list():array
    {
        R::selectDatabase("courses_list");
        $courses_list = R::findAll("courses");
        R::selectDatabase("default");
        return $courses_list;
    }
}
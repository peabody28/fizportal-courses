<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";


class Courses_table implements Table
{
    public function create($course)
    {
        $row = R::dispense("courses");
        $row->title = $course->title;
        $row->text = $course->text;
        $row->complexity = $course->complexity;
        $row->price = $course->price;
        $course->id = R::store($row);
        return $course->id ? true: false;
    }
    public function read($course)
    {
        $row =  R::findOne("courses", "id = ?", [$course->id]);
        return $row;
    }
    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }
    public function delete($course)
    {
        $row =  R::load("courses", $course->id);
        R::trash($row);
    }
    public function get_courses_list():array
    {
        $courses_list = R::findAll("courses");
        return $courses_list;
    }
}
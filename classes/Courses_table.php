<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";

$link = mysqli_connect("127.0.0.1", "root", "1234", "fizportal_courses");
class Courses_table implements Table
{
    public function create($course)
    {
        global $link;
        $sql = sprintf("INSERT INTO courses(title, text, complexity, price) VALUES ('%s', '%s', '%s', '%s')", $course->title, $course->text, $course->complexity, $course->price);
        $result = mysqli_query($link, $sql);
        $course->id = mysqli_insert_id($link);
        return $course->id ? true: false;
    }
    public function read($course)
    {
        $row =  R::findOne("courses", "id = ?", [$course->id]);
        return $row;
    }
    public function update($course, $column)
    {
        $row =  R::findOne("courses", "id = ?", [$course->id]);
        $row->$column = $course->$column;
        $status = R::store($row);
        return $status?true:false;
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
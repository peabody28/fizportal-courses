<?php
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
    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM courses WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $course_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $course_data;
    }
    public function update($course, $column)
    {
        global $link;
        $sql = sprintf("UPDATE courses SET %s='%s' WHERE id = '%s'", $column, $course->$column, $course->id);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function delete($id)
    {
        global $link;
        $sql = sprintf("DELETE FROM courses WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function get_courses_list():array
    {
        global $link;
        $sql = sprintf("SELECT * FROM courses");
        $result = mysqli_query($link, $sql);
        $courses_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $courses_list;
    }
}
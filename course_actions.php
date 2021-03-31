<?php
require_once __DIR__."/classes/Course.php";
$data = $_POST;
// обработка действий админа
if (isset($data["submit"]))
{

    if ($data["code"]=="create_course")
    {
        $course = new Course();
        $course->name = $data["course_name"];
        $course->title = $data["title"];
        $course->add();
    }
    else if($data["code"]=="del_course")
    {
        $course = new Course();
        $course->id = $data["id"];
        $course->delete();
    }
}
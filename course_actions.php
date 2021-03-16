<?php
require_once "db.php";
require_once "classes/Course.php";

$course_data = $_POST;

if (isset($course_data["submit"]))
{
    R::selectDatabase("courses_list");
    //switch
    if ($course_data["code"]=="add")
    {
        $course = new Course($course_data["cours_name"], $course_data["title"]);
        echo $course->add();
    }
    else if($course_data["code"]=="rm")
    {
        $course = new Course();
        $status = $course->search($course_data["id"]);
        if($status)
        {
            $course->id = $course_data["id"];
            $course->remove();
            echo json_encode(["status"=>"OK"]);
        }
        else
            echo json_encode(["status"=>"ERROR", "error"=>"Такого курса не существует"]);
    }
    R::selectDatabase("default");
}
else
    header("Location: /main.php");


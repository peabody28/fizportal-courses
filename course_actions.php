<?php
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Theme.php";
$data = $_POST;
// обработка действий админа
if (isset($data["submit"]))
{

    if ($data["code"]=="create_course")
    {
        $course = new Course();
        $course->title = $data["course_title"];
        $course->text = $data["course_text"];
        $course->add();
    }
    else if($data["code"]=="del_course")
    {
        $course = new Course();
        $course->id = $data["id"];
        $course->delete();
    }
    else if($data["code"]=="add_theme")
    {
        $theme = new Theme();
        $theme->title = $data["theme_title"];
        $theme->text = $data["theme_text"];
        $theme->course_id = $data["course_id"];
        $theme->add();
        // тема уже лежит в базе
        $course = new Course();
        $course->id = $data["course_id"];
        $course->get();

    }
}
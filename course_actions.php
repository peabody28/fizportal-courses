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
    else if($data["code"]=="add_theme")
    {
        $theme = new Theme();
        $theme->name = $data["theme_name"];
        $theme->add();
        // тема уже лежит в базе
        $course = new Course();
        $course->id = $data["course_id"];
        $course->get();
        $themes = json_decode($course->themes);
        array_push($themes, $theme->id);
        $course->themes = $themes;
        $course->add_theme_to_course();
    }
}
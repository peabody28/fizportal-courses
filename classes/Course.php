<?php
require_once __DIR__."/../db.php";
require_once __DIR__ . "/Courses_table.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Themes_table.php";



class Course
{
    public $id, $title, $text, $complexity=0, $price=null;
    function get()
    {
        $courses_db = new Courses_table();
        $course = $courses_db->read($this);
        if($course)
        {
            $this->id = $course->id;
            $this->title = $course->title;
            $this->text = $course->text;
            $this->price = $course->price;
            $this->existence = true;
        }
    }
}

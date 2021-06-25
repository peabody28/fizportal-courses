<?php
require_once __DIR__ . "/Courses_table.php";


class Course
{
    public $id, $title, $text, $complexity=0, $price=null;
    public function get()
    {
        $courses_db = new Courses_table();
        $course = $courses_db->read($this->id);
        if($course)
        {
            $this->title = $course["title"];
            $this->text = $course["text"];
            $this->price = $course["price"];
            $this->complexity = $course["comlexity"];
        }
        return $this;
    }
}

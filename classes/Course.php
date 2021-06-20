<?php
require_once __DIR__."/../db.php";
require_once __DIR__ . "/Courses_table.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Themes_table.php";



class Course
{
    public $id, $title, $text, $complexity=0, $price=null, $existence=false;

    public function add()
    {
        $courses_db = new Courses_table();
        $response = $courses_db->create($this);
        //поверка на успешность добавления
    }
    public function get()
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
    public function add_theme()
    {
        $themes_table = new Themes_table();
        $themes_table->create();
    }
    public function delete()
    {
        $courses_db = new Courses_table();
        $courses_db->delete($this);
    }

    public function get_themes()
    {
        $themes_table = new Themes_table();
        $themes_list =  $themes_table->get_themes_course($this->id);
        return $themes_list;
    }

}

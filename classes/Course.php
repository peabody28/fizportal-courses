<?php
require_once __DIR__ . "/Courses_table.php";
require_once __DIR__ . "/Themes_table.php";


class Course
{
    public $id, $title, $text, $complexity=0, $price=null, $img_url=null;

    public function __construct($id=null)
    {
        if($id !== null)
        {
            $courses_table = new Courses_table();
            $tmp_course = $courses_table->read($id);
            $this->id = (int)$tmp_course["id"];
            $this->title = $tmp_course["title"];
            $this->text = $tmp_course["text"];
            $this->complexity = (int)$tmp_course["complexity"];
            $this->price = $tmp_course["price"];
            $this->img_url = $tmp_course["img_url"];
        }

    }

    public function get_themes_ids()
    {
        $themes_table = new Themes_table();
        $themes_ids_list = $themes_table->get_courses_themes($this->id);
        return $themes_ids_list;
    }
}

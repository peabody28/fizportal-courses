<?php
require_once __DIR__."/../db.php";
require_once __DIR__ . "/Courses_table.php";
require_once __DIR__."/Theme.php";



class Course
{
    public $id, $name, $title, $themes, $existence=false;

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
            $this->name = $course->name;
            $this->title = $course->title;
            $this->themes = json_decode($course->themes);
            $this->existence = true;
        }
    }
    public function add_theme()
    {
        $courses_db = new Courses_table();
        $courses_db->update($this, "themes");
    }
    public function get_themes():array
    {
        if(!$this->themes)
            $this->get();
        $themes_list = [];
        foreach ($this->themes as $item)
        {
            $theme = new Theme();
            $theme->id = $item;
            $theme->get();
            array_push($themes_list,  ["name"=>$theme->name, "id"=>$theme->id]);
        }
        return $themes_list;
    }

    public function delete()
    {
        $courses_db = new Courses_table();
        $courses_db->delete($this);
    }

}

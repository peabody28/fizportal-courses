<?php
require_once "db.php";
require_once "classes/Courses_db.php";
require_once "classes/Render.php";

class Course
{
    public $id, $name, $title, $themes, $existence;

    public function add()
    {
        $courses_db = new Courses_db();
        $courses_db->add($this);
    }
    public function get()
    {
        $courses_db = new Courses_db();
        $courses_db->get_course($this);
        return $this;
    }
    public function get_courses(): array
    {
        $courses_db = new Courses_db();
        $courses_list = $courses_db->get_courses_list();
        return $courses_list;
    }
    public function get_html(array $courses_list)
    {
        $render = new Render();
        $render_courses_list = $render->render_cours($courses_list);
        return $render_courses_list;
    }
    public function delete()
    {
        $courses_db = new Courses_db();
        $courses_db->delete($this);
    }


}

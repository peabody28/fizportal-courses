<?php
require_once __DIR__."/Courses_db.php";
require_once __DIR__."/Render.php";


class Theme
{
    public $id, $name, $level, $tasks, $existence=false;
    public function add()
    {
        $courses_db = new Courses_db();
        $courses_db->add_theme($this);
    }
    public function get()
    {
        $courses_db = new Courses_db();
        $courses_db->get_theme($this);
    }
    public function get_html(array $themes)
    {
        $themes_list = [];
        foreach ($themes as $item)
        {
            $theme = new Theme();
            $theme->id = $item;
            $theme->get();
            array_push($themes_list, $theme);
        }
        $render = new Render();
        return $render->render_theme($themes_list);
    }
}
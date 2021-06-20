<?php
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__."/Render.php";


class Theme
{
    public $id, $title, $complexity=0, $course_id, $existence=false;
    public function add()
    {
        $themes_table = new Themes_table();
        $response = $themes_table->create($this);
        // проверка на успешность добавления
    }
    public function get()
    {
        $themes_table = new Themes_table();
        $theme = $themes_table->read($this);
        if($theme)
        {
            $this->title = $theme->title;
            $this->complexity = $theme->complexity;
            $this->existence=true;
        }
    }
}
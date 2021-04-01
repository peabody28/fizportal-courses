<?php
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__."/Render.php";


class Theme
{
    public $id, $name, $level, $tasks, $existence=false;
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
            $this->name = $theme->name;
            $this->tasks = json_decode($theme->tasks);
            $this->level = $theme->level;
            $this->existence=true;
        }
    }
}
<?php
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__ . "/Themes_points_limit_table.php";


class Theme
{
    public $id, $title, $complexity=0, $points_limit=10;

    public function __construct($id=null)
    {
        if ($id!==null)
        {
            $themes_table = new Themes_table();
            $tmp_theme = $themes_table->read($id);
            $this->id = $tmp_theme["id"];
            $this->title = $tmp_theme["title"];
            $this->complexity = $tmp_theme["complexity"];
            $this->course_id = $tmp_theme["course_id"];
        }
    }

    public function get_tasks()
    {
        $tasks_table = new Tasks_table();
        $tasks_list = $tasks_table->get_tasks_theme($this->id);
        return $tasks_list;
    }

    public function get_points_limit()
    {
        $themes_points_limit_table = new Themes_points_limit_table();
        $resp = $themes_points_limit_table->read($this->id);
        $this->points_limit = $resp["points_limit"]?:10;  // если лимит не установен, принимаем его за 10 баллов
    }

}
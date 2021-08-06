<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__ . "/Themes_points_limit_table.php";
require_once __DIR__ . "/Themes_limits_table.php";



class Theme
{
    public $id, $title, $text, $complexity=0, $course_id, $points_limit=10, $time_limit=null, $tasks=null;

    public function __construct($id=null, $tmp_theme=null)
    {
        if ($id!==null)
        {
            if(!$tmp_theme)
            {
                $themes_table = new Themes_table();
                $tmp_theme = $themes_table->read($id);
            }
            $this->id = $tmp_theme["id"];
            $this->title = $tmp_theme["title"];
            $this->text = $tmp_theme["text"];
            $this->complexity = $tmp_theme["complexity"];
            $this->course_id = $tmp_theme["course_id"];
        }
    }

    public function get_tasks()
    {
        $tasks_table = new Tasks_table();
        $tasks_list = $tasks_table->get_tasks_theme($this->id);
        foreach ($tasks_list as $item) {
            $task = new Task($item["id"], $item);
            $this->tasks[] = $task;
        }
        return $this->tasks;
    }

    public function get_tasks_ids()
    {
        $list = [];
        if(!$this->tasks)
            $this->get_tasks();
        foreach ($this->tasks as $task) {
            $list[]= $task->id;
        }
        return $list;
    }

    public function get_points_limit()
    {
        $themes_points_limit_table = new Themes_points_limit_table();
        $resp = $themes_points_limit_table->read($this->id);
        $this->points_limit = (int)($resp["points_limit"]?:10);  // если лимит не установен, принимаем его за 10 баллов
    }

    public function get_time_limit()
    {
        $themes_limits_table = new Themes_limits_table();
        $answ = $themes_limits_table->read($this->id);
        $this->time_limit = $answ?(int)$answ["time_limit"]:null;
    }

}
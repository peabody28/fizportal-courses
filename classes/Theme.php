<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Supertest.php";
require_once __DIR__ . "/Themes_table.php";
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__ . "/Themes_points_limit_table.php";
require_once __DIR__ . "/Themes_limits_table.php";
require_once __DIR__ . "/HTML_block.php";
require_once __DIR__ . "/Render.php";

class Theme implements HTML_block
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
        $this->points_limit = (int)$resp["points_limit"];  // если лимит не установен, принимаем его за 10 баллов
        return $this->points_limit;
    }

    public function get_time_limit()
    {
        $themes_limits_table = new Themes_limits_table();
        $answ = $themes_limits_table->read($this->id);
        $this->time_limit = $answ?(int)$answ["time_limit"]:null;
        return $this->time_limit;
    }

    public function get_next_task_id($task)
    {
        $themes_tasks_ids = $this->get_tasks_ids();
        $task_number_in_theme = array_search($task->id, $themes_tasks_ids);
        $next_task_id = $themes_tasks_ids[$task_number_in_theme+1];
        return $next_task_id ?? null;
    }

    public function get_html($data)
    {
        $render = new Render();
        $theme_block = $render->render_theme($this, $data["class"], $data["progress"], $data["is_admin"]);
        return $theme_block["block"];
    }

    public function get_text_html()
    {
        $block = "<div class='col-12 m-0 p-0 text-break'>$this->text</div>";
        return ["block"=>$block];

    }

    public function get_tasks_blocks($user)
    {
        // получаю задачи темы
        $this->get_tasks();
        // супертест
        $sptest = new Supertest($this->id);
        // рендер блоков задач и супертеста
        $render = new Render();
        $response = $render->render_tasks_theme($this, $this->tasks, $user, $sptest);
        $response["tasks_count"] = count($this->tasks);
        return $response;
    }
}
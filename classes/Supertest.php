<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Supertests_table.php";
require_once __DIR__."/Supertests_tasks_table.php";


class Supertest
{
    public $id, $theme_id;

    public function __construct($theme_id=null)
    {
        if($theme_id!==null)
        {
            $supertests_table = new Supertests_table();
            $tmp_sptest = $supertests_table->read_by_theme($theme_id);
            $this->id = $tmp_sptest["id"];
            $this->theme_id = $tmp_sptest["theme_id"];
        }
    }

    public function get_tasks()
    {
        $list = [];
        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_rows = $supertests_tasks_table->read($this->id);
        foreach ($supertests_tasks_rows as $item)
        {
            $task = new Task($item["task_id"]);
            $list[] = $task;
        }
        return $list;
    }
}
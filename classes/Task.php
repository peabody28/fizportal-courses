<?php
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__."/Render.php";


class Task
{
    public $id, $text, $answer, $complexity=0, $theme_id;
    public function get()
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($this);
        if($task)
        {
            $this->text = $task->text;
            $this->answer = $task->answer;
            $this->complexity = $task->complexity;
            $this->theme_id = $task->theme_id;
        }
    }
}
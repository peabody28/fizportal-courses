<?php
require_once __DIR__ . "/Tasks_table.php";


class Task
{
    public $id, $text, $answer=null, $complexity=0, $theme_id, $type=null, $img_url=null;
    public function get()
    {
        $tasks_table = new Tasks_table();
        $task = $tasks_table->read($this->id);
        if($task)
        {
            $this->text = $task["text"];
            $this->answer = $task["answer"];
            $this->complexity = $task["complexity"];
            $this->theme_id = $task["theme_id"];
        }
    }
}
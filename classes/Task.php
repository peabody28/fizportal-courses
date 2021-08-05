<?php
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__ . "/Tasks_answers_table.php";



class Task
{
    public $id, $text, $answer=null, $complexity=0, $theme_id=0, $type=null, $img_url=null, $users_answer=null;

    public function __construct($id=null, $tmp_task=null)
    {
        if($id !== null)
        {
            if(!$tmp_task)
            {
                $tasks_table = new Tasks_table();
                $tmp_task = $tasks_table->read($id);
            }
            $this->id = $tmp_task["id"];
            $this->text = $tmp_task["text"];
            $this->answer = $tmp_task["answer"]?:null; // todo answer_construct
            $this->complexity = $tmp_task["complexity"];
            $this->theme_id = $tmp_task["theme_id"];
            $this->type = $tmp_task["type"];
            $this->img_url = $tmp_task["img_url"];
        }
    }

    public function get_answer()
    {
        $tasks_answers_table = new Tasks_answers_table();
        $this->answer = $tasks_answers_table->read($this->id);
    }
}
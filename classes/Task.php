<?php
require_once __DIR__ . "/Tasks_table.php";
require_once __DIR__ . "/Tasks_answers_table.php";
require_once __DIR__ . "/Render.php";



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
            $this->answer = $tmp_task["answer"]?:null;
            $this->complexity = $tmp_task["complexity"];
            $this->theme_id = $tmp_task["theme_id"];
            $this->type = $tmp_task["type"];
            $this->img_url = $tmp_task["img_url"];
        }
    }

    public function get_A_answer()
    {
        $tasks_answers_table = new Tasks_answers_table();
        $resp = $tasks_answers_table->read($this->id);
        foreach ($resp as $item)
            $this->answer[] = $item["answer"];

    }

    public function get_html($data)
    {
        $is_admin = $data["is_admin"];

        $block = new Render();
        $task_block = $block->render_task($this, 0);
        if ($is_admin)
        {
            $task_block .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$this->id'>Изменить задачу</a></div>";
            $task_block .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($this->id);return false;'>Удалить эту задачу</button>
                             </div>";
        }
        // материалы для задачи
        $task_block .= "<div class='d-flex justify-content-center col-12 mt-3' id='message'></div>";
        $task_block .= "<div class='col-12 d-flex justify-content-center'> <a href='/materials?task_id=$this->id'>Материалы для задачи</a></div>";

        return ["block"=>$task_block];
    }
}
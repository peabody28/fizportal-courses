<?php
require_once __DIR__."/Render.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/HTML_block.php";
require_once __DIR__."/Task.php";
// TODO НЕ РАБОТАЕТ EXTENDS
class Mistake extends Task
{
    /*
    public function __construct($id=null, $tmp_task=null)
    {
        if($id !== null)
        {
            if(!$tmp_task)
            {
                $tasks_table = new Tasks_table();
                $tmp_task = $tasks_table->read($id);
            }
            $this->id = (int)$tmp_task["id"];
            $this->text = $tmp_task["text"];
            $this->answer = $tmp_task["answer"]?:null;
            $this->complexity = (int)$tmp_task["complexity"];
            $this->theme_id = (int)$tmp_task["theme_id"];
            $this->type = $tmp_task["type"];
            $this->img_url = $tmp_task["img_url"];
        }
    }
*/
    public function get_html($data)
    {
        $is_admin = $data["is_admin"];

        $block = new Render();

        $task_block = $block->render_mistake($this);
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

    public function send_answer($data)
    {
        $task = $this->construct_task_for_professor($data);

        $professor = new Professor();
        $professor->student = $data["user"];

        $status = $professor->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $professor->add_task_to_users_tasks($task);
            // добавляю балл в тему
            $task->complexity = (int)($task->complexity/2) + $task->complexity;
            $professor->add_point($task);
            // удаляю из РО
            $professor->delete_from_mistakes($task);

            return ["status" => "OK", "task_id"=>$task->id];
        }
        else
            return ["status" => "ERROR", "task_id"=>$task->id];
    }
}
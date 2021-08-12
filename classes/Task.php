<?php
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Tasks_answers_table.php";
require_once __DIR__."/HTML_block.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Render.php";
require_once __DIR__."/Timer.php";
require_once __DIR__."/Theme.php";


class Task // implements HTML_block TODO ЧЕГОТО НЕ РАБОТАЕТ implements
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
            $this->id = (int)$tmp_task["id"];
            $this->text = $tmp_task["text"];
            $this->answer = $tmp_task["answer"]?:null;
            $this->complexity = (int)$tmp_task["complexity"];
            $this->theme_id = (int)$tmp_task["theme_id"];
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

    public function send_answer($data)
    {
        // TODO проверить этот метод
        $task = $this->construct_task_for_professor($data);
        $user = &$data["user"];
        $theme = new Theme($this->theme_id);

        // Если задача в РО, отклоняю решение
        $professor = new Professor();
        $in_mistakes = $professor->check_in_mistakes_list($user, $task);
        if($in_mistakes)
            return ["status" => "ERROR", "code"=>"IN_MISTAKES"];

        // Проверяю время
        $timer = new Timer();
        $response = $timer->check_time($user, $theme);
        if($response["status"]===false)
            return ["status" => "ERROR", "code"=>"TIME"];
        else if ($response["status"]==="update")
            $timer->set_theme_begin_time($user, $theme);

        $status = $professor->check_task($task);
        if($status)
        {
            $status = $professor->add_task_to_users_tasks($user, $task);
            if($status) // если решается впервые добавляю балл
                $professor->add_point($user, $task);
            // это нужно в js для открытия супертеста
            $progress = $professor->get_points($user, $theme);
            $theme->get_points_limit();

            return ["status" => "OK", "task_id"=>$task->id, "points_limit"=>$theme->points_limit, "progress"=>$progress];
        }
        else
        {
            $status = $professor->add_to_mistakes($user, $task); // добавляю задачу в РО
            if ($status) // true если задача не была в РО
                $professor->delete_point($user, $task); // снимаю балл

            return ["status" => "ERROR", "task_id"=>$task->id];
        }
    }

    public function construct_task_for_professor($data)
    {
        $task = &$this;

        if($task->type == "A")
        {
            $task->get_A_answer();

            $task->users_answer = [];
            for($i=1; $i<=5; $i++)
            {
                if (isset($data[$task->id."_a_answ$i"]))
                    $task->users_answer[] = $i;
            }
        }
        else
            $task->users_answer = $data[$task->id."_b_answer"];
        return $task;
    }
}
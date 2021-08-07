<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Render.php";
require_once __DIR__ . "/Professor.php";
require_once __DIR__ . "/Professor_mistakes.php";


class Mistake extends Task
{
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
        // TODO проверить этот метод
        $task = $this->construct_task_for_professor($data);
        $user = &$data["user"];

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $prof->add_task_to_users_tasks($user, $task);
            // добавляю балл в тему
            $prof->add_point($user, $task);
            // удаляю из РО
            $prof_mist = new Professor_mistakes();
            $prof_mist->delete_from_mistakes($user, $task);

            return ["status" => "OK", "task_id"=>$task->id];
        }
        else
            return ["status" => "ERROR", "task_id"=>$task->id];
    }
}
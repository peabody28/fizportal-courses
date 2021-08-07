<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Professor.php";
require_once __DIR__ . "/Render.php";

class Supertest_task extends Task
{
    public function get_html($data)
    {
        $render = new Render();
        $sp_task_block = $render->render_supertest_task($this, $data["is_admin"]);
        return $sp_task_block;
    }

    public function send_answer($data)
    {
        // TODO проверить этот метод
        $task = $this->construct_task_for_professor($data);
        $user = &$data["user"];

        $prof = new Professor();
        $status = $prof->check_task($task);
        return $status;
    }
}
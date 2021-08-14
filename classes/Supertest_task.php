<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Professor.php";
require_once __DIR__ . "/Render.php";


class Supertest_task extends Task
{
    public function get_html($data)
    {
        $render = new Render();
        $sp_task_block = $render->render_supertest_task($this, $data["theme_id"], $data["is_admin"]);
        return $sp_task_block;
    }

    public function send_answer($data)
    {
        // TODO проверить этот метод
        $task = $this->construct_task_for_professor($data);

        $prof = new Professor();
        $prof->student = $data["user"];

        $status = $prof->check_task($task);
        return $status;
    }
}
<?php
require_once __DIR__ . "/Task.php";
require_once __DIR__ . "/Render.php";

class Supertest_task extends Task
{
    public function get_html($data)
    {
        $render = new Render();
        $sp_task_block = $render->render_supertest_task($this, $data["is_admin"]);
        return $sp_task_block;
    }
}
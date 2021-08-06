<?php
require_once __DIR__."/User.php";
require_once __DIR__."/Theme.php";
require_once __DIR__."/Task.php";
require_once __DIR__."/Professor.php";
require_once __DIR__."/Supertest_task.php";
require_once __DIR__."/Supertests_table.php";
require_once __DIR__."/Supertests_tasks_table.php";


class Supertest
{
    public $id, $theme_id, $tasks;

    public function __construct($theme_id=null)
    {
        if($theme_id!==null)
        {
            $supertests_table = new Supertests_table();
            $tmp_sptest = $supertests_table->read_by_theme($theme_id);
            $this->id = $tmp_sptest["id"];
            $this->theme_id = $tmp_sptest["theme_id"];
        }
    }

    public function get_tasks()
    {
        $this->tasks = [];
        $supertests_tasks_table = new Supertests_tasks_table();
        $supertests_tasks_rows = $supertests_tasks_table->read($this->id);
        foreach ($supertests_tasks_rows as $item)
        {
            $sp_task = new Supertest_task($item["task_id"]);
            $this->tasks[] = $sp_task;
        }
        return $this->tasks;
    }

    public function get_html($data)
    {
        $is_admin = $data["is_admin"];

        $supertests_tasks = $this->get_tasks();
        $supertest_block = "";
        if ($is_admin)
            $supertest_block .= "<div class='col-12 d-flex justify-content-center mb-5'><a class='btn add_task_to_supertest_btn' href='/add_task?supertest_id=$this->id'>Добавить задачу в супертест</a></div>";
        if ($supertests_tasks)
        {
            $supertest_block .= "<form class='col-12 send_supertest_answers'  method='POST' onsubmit='send_supertest_answers();return false;'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='send_supertest_answers'>
                                    <input type='hidden' name='theme_id' value='$this->theme_id'>";
            foreach ($supertests_tasks as $task)
                $supertest_block .= $task->get_html(["is_admin"=>$is_admin]);

            $supertest_block .= "<hr><div class='m-0 col-12 mt-5 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>";
            $supertest_block .= "<div class='col-12 mt-3 d-flex justify-content-center h2' id='message'></div>";
            $supertest_block .= "</form>";
        }

        return ["block"=>$supertest_block];
    }
}
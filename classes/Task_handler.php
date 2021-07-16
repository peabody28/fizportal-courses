<?php
require_once __DIR__."/Professor.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_mistakes_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Tasks_answers_table.php";


class Task_handler
{
    public $data;


    public function send_answer()
    {
        $task = $this->construct_task();

        $prof = new Professor();
        $status_time = $prof->check_time(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
        if(!$status_time)
            return ["status" => "ERROR", "code"=>"TIME"];
        $status = $prof->check_task($task);

        $users_tasks_table = new Users_tasks_table();
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $st = $users_tasks_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);

            $users_progress_theme_table = new Users_progress_theme_table();
            if($st) // если решается впервые  добавляю балл
                $users_progress_theme_table->add_point(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
            $resp = $users_progress_theme_table->read(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
            return ["status" => "OK", "task_id"=>$this->data["task_id"], "progress"=>$resp["progress"]?:null];
        }
        else
        {
            $users_tasks = $users_tasks_table->read($_SESSION["id"]);

            if(!in_array(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]], $users_tasks)) // если пользователь эту задачу еще не решал
            {
                // добавляю задачу в РО
                $users_mistakes_table = new Users_mistakes_table();
                $st = $users_mistakes_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);
                // если $st==true то задачу первый раз решили неверно и я могу снять балл
                $users_themes_table = new Users_themes_table();
                $users_themes = $users_themes_table->read($_SESSION["id"]);

                if($st && !in_array(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]], $users_themes)) // если тема не выполнена
                {
                    // Cнимаю балл за неверное решение
                    $users_progress_theme_table = new Users_progress_theme_table();
                    $users_progress_theme_table->delete_point(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
                }

            }
            return ["status" => "ERROR"];
        }

    }

    public function send_mistake_answer()
    {
        $task = $this->construct_task();

        $prof = new Professor();
        $status = $prof->check_task($task);
        if($status)
        {
            // добавляю задачу в список решенных пользователем
            $users_tasks_table = new Users_tasks_table();
            $users_tasks_table->create(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);
            // добавляю балл НУЖНО ЛИ? ТЕМА И ТАК ВЫПОЛНЕНА НА ЭТОМ ЭТАПЕ
            $users_progress_theme_table = new Users_progress_theme_table();
            $users_progress_theme_table->add_point(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]], 2);
            // удаляю из РО
            $users_mistakes_table = new Users_mistakes_table();
            $users_mistakes_table->delete(["user_id"=>$_SESSION["id"], "task_id"=>$this->data["task_id"]]);

            return ["status" => "OK"];
        }
        else
            return ["status" => "ERROR"];
    }

    public function send_supertest_answer()
    {
        $str = "";
        foreach ($this->data as $key => $val)
        {
            if($key=="code" || $key == "submit")
                continue;
            $str .= "&".$key."=";
        }
        $match = [];
        preg_match_all("/&([0-9]*)_{1}a{0,1}b{0,1}_{1}/u", $str, $match);

        $tasks = [];

        foreach (array_unique($match[1]) as $task_id)
        {
            $this->data["task_id"] = $task_id;
            $task = $this->construct_task();
            array_push($tasks, $task);
        }

        // проверка
        $status = true;
        $prof = new Professor();
        foreach ($tasks as $item)
        {
            $status = $prof->check_task($item);
            if(!$status)
                break;
        }
        if ($status)
        {
            $users_themes_table = new Users_themes_table();
            $users_themes_table->create(["user_id"=>$_SESSION["id"], "theme_id"=>$this->data["theme_id"]]);
            return ["status"=>"OK"];
        }
        else
            return ["status"=>"ERROR"];

    }

    public function construct_task()
    {
        $id = $this->data["task_id"];

        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($id);

        $task = ["id"=>$tmp_task["id"], "type"=>$tmp_task["type"]];
        if($tmp_task["type"]=="A")
        {
            $tasks_answers_table = new Tasks_answers_table();
            $task["answers"] = $tasks_answers_table->read($task["id"]);

            $task["user_answers"] = [];
            for($i=1; $i<=5; $i++)
                if (isset($this->data["$tmp_task[id]_a_answ$i"]))
                    $task["user_answers"][] = $i;
        }
        else
        {
            $task["answer"] =  $tmp_task["answer"];
            $task["user_answer"]=$this->data["$tmp_task[id]_b_answer"];
        }
        return $task;
    }

}
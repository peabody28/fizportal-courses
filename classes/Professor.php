<?php
require_once __DIR__."/Tasks_answers_table.php";

class Professor
{
    public function check_task($task)
    {
        $status = true;

        if ($task["type"]=="A") {
            for($i=1; $i<=5;$i++)
            {
                if( (!in_array(["task_id"=>$task["id"], "answer"=>$i], $task["answers"]) && in_array($i, $task["user_answers"]) ) ||
                    (in_array(["task_id"=>$task["id"], "answer"=>$i], $task["answers"]) && !in_array($i, $task["user_answers"])) )
                {
                    $status=false;
                    break;
                }
            }
        }
        else
            $status = ($task["answer"]==$task["user_answer"]);

        return $status;
    }
}
<?php
require_once __DIR__."/Table.php";


class Tasks_answers_table implements Table
{

    public function create($task_answer)
    {
        global $link;
        $sql = sprintf("INSERT INTO tasks_answers(task_id, answer) VALUES ('%s', '%s')", $task_answer["task_id"], $task_answer["answer"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($task_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM tasks_answers WHERE task_id='%s'", $task_id);
        $res = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return $rows;
    }

    public function update($obj, $column)
    {
    }

    public function delete($task_id)
    {
        global $link;
        $sql = sprintf("DELETE FROM tasks_answers WHERE task_id='%s'", $task_id);
        $res = mysqli_query($link, $sql);
        return $res;
    }
}
<?php
require_once __DIR__."/Table.php";


class Users_tasks_table implements Table
{
    public function create($users_tasks)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_tasks WHERE user_id='%s' AND task_id='%s'", $users_tasks["user_id"], $users_tasks["task_id"]);
        $res = mysqli_query($link, $sql);
        if($res->num_rows)
            return false;
        else
        {
            $sql = sprintf("INSERT INTO users_tasks(user_id, task_id) VALUES ('%s', '%s')", $users_tasks["user_id"], $users_tasks["task_id"]);
            $result = mysqli_query($link, $sql);
        }
        return $result;
    }
    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_tasks WHERE user_id='%s'", $id);
        $res = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return $rows;
    }
    public function update($obj, $column)
    {
    }
    public function delete($obj)
    {
        global $link;
        $sql = sprintf("DELETE FROM users_tasks WHERE user_id='%s' AND task_id='%s'", $obj["user_id"], $obj["task_id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function get_users_tasks($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_tasks WHERE user_id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $users_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $users_tasks;
    }

}
<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";
$link = mysqli_connect("127.0.0.1", "root", "1234", "fizportal_courses");


class Users_tasks_table implements Table
{
    public function create($users_tasks)
    {
        global $link;

        $sql1 = "SELECT * FROM users_tasks WHERE user_id=" . $users_tasks->user_id . " AND task_id=". $users_tasks->task_id . "";
        $res = mysqli_query($link, $sql1);
        if($res->num_rows)
        {
            return false;
        }
        else
        {
            $sql = "INSERT INTO users_tasks(user_id, task_id) VALUES ($users_tasks->user_id, $users_tasks->task_id);";
            $result = mysqli_query($link, $sql);
        }
        return $result;
    }
    public function read($obj)
    {
        // TODO: Implement read() method.
    }
    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }
    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }

}
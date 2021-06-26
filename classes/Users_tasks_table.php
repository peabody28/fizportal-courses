<?php
require_once __DIR__."/Table.php";


$link = mysqli_connect("127.0.0.1", "root", "1234", "fizportal_courses");


class Users_tasks_table implements Table
{
    public function create($users_tasks)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_tasks WHERE user_id='%s' AND task_id='%s'", $users_tasks->user_id, $users_tasks->task_id);
        $res = mysqli_query($link, $sql);
        if($res->num_rows)
            return false;
        else
        {
            $sql = sprintf("INSERT INTO users_tasks(user_id, task_id) VALUES ('%s', '%s')", $users_tasks->user_id, $users_tasks->task_id);
            $result = mysqli_query($link, $sql);
        }
        return $result;
    }
    public function read($id)
    {
        // TODO: Implement read() method.
    }
    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

}
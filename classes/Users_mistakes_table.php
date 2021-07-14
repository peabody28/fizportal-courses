<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";


class Users_mistakes_table implements Table
{

    public function create($obj)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_mistakes WHERE user_id='%s' AND task_id='%s'", $obj["user_id"], $obj["task_id"]);
        $res = mysqli_query($link, $sql);
        if($res->num_rows)
            return false;
        else
        {
            global $link;
            $sql = sprintf("INSERT INTO users_mistakes(user_id, task_id) VALUES ('%s', '%s')", $obj["user_id"], $obj["task_id"]);
            $result = mysqli_query($link, $sql);
            return $result;
        }

    }

    public function read($user_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_mistakes WHERE user_id='%s'", $user_id);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result);
        return $row;
    }

    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }

    public function delete($obj)
    {
        global $link;
        $sql = sprintf("DELETE FROM users_mistakes WHERE user_id='%s' AND task_id='%s'", $obj["user_id"], $obj["task_id"]);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result);
        return $row;
    }
}
<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";


class Users_themes_time implements Table
{

    public function create($obj)
    {
        global $link;
        $sql = sprintf("INSERT INTO users_themes_time(user_id, theme_id, time) VALUES ('%s', '%s', '%s')", $obj["user_id"], $obj["theme_id"], $obj["time"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($obj)
    {
        global $link;
        $sql = sprintf("SELECT time FROM users_themes_time WHERE user_id = '%s' AND theme_id='%s'", $obj["user_id"], $obj["theme_id"]);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
    }

    public function update($obj, $column)
    {
        if ($this->read($obj))
        {
            global $link;
            $sql = sprintf("UPDATE users_themes_time SET time = '%s' WHERE user_id = '%s' AND theme_id='%s'", $obj["time"], $obj["user_id"], $obj["theme_id"]);
            $result = mysqli_query($link, $sql);
            return $result;
        }
        else
            $this->create($obj);
    }

    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }
}
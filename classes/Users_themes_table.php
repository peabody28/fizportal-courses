<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";


class Users_themes_table implements Table
{

    public function create($users_theme)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_themes WHERE user_id='%s' AND theme_id='%s'", $users_theme["user_id"], $users_theme["theme_id"]);
        $res = mysqli_query($link, $sql);
        if($res->num_rows)
            return false;
        else
        {
            $sql = sprintf("INSERT INTO users_themes(user_id, theme_id) VALUES ('%s', '%s', '%s')", $users_theme["user_id"], $users_theme["theme_id"]);
            $result = mysqli_query($link, $sql);
        }
        return $result;
    }

    public function read($user_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_themes WHERE user_id='%s'", $user_id);
        $res = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return $rows;
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
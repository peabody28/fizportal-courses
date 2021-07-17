<?php
require_once __DIR__."/Table.php";


class Users_courses_table implements Table
{

    public function create($users_courses)
    {
        global $link;
        $sql = sprintf("INSERT INTO users_courses(user_id, course_id) VALUES ('%s', '%s')", $users_courses["user_id"], $users_courses["course_id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($user_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_courses WHERE user_id='%s'", $user_id);
        $res = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
        return $rows;
    }

    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }

    public function delete($user_courses_row_id)
    {
        // TODO: Implement delete() method.
    }
}
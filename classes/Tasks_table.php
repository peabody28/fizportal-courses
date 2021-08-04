<?php
require_once __DIR__."/Table.php";


class Tasks_table implements Table
{

    public function create($task)
    {
        global $link;
       // $text = mysqli_escape_string($task->text);
        $sql = sprintf("INSERT INTO tasks(text, answer, img_url, complexity, theme_id, type) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')", addslashes($task->text), strip_tags($task->answer), $task->img_url, $task->complexity, $task->theme_id, $task->type);
        $result = mysqli_query($link, $sql);
        $task->id = mysqli_insert_id($link);
        return $task->id ? true: false;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM tasks WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $task_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $task_data;
    }

    public function update($task, $column)
    {
        global $link;
        $sql = sprintf("UPDATE tasks SET %s='%s' WHERE id = '%s'", $column, addslashes($task->$column), $task->id);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function delete($id)
    {
        global $link;
        $sql = sprintf("DELETE FROM tasks WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function get_tasks_theme($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM tasks WHERE theme_id='%s'", $id);
        $result = mysqli_query($link, $sql);
        $tasks_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $tasks_list;
    }
}
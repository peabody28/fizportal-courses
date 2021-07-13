<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";

class Tasks_files_table implements Table
{

    public function create($materials)
    {
        global $link;
        $sql = sprintf("INSERT INTO tasks_files(task_id, url) VALUES ('%s', '%s')", $materials["task_id"], $materials["url"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($task_id)
    {
        global $link;
        $sql = sprintf("SELECT url FROM tasks_files WHERE task_id='%s'", $task_id);
        $result = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";

class Tasks_materials_table implements Table
{

    public function create($task_id)
    {
        global $link;
        $sql = sprintf("INSERT INTO tasks_materials(task_id) VALUES ('%s')", $task_id);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($task_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM tasks_materials WHERE task_id='%s'", $task_id);
        $result = mysqli_query($link, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $rows;
    }

    public function update($materials, $column)
    {
        if(!$this->read($materials["task_id"]))
            $this->create($materials["task_id"]);
        global $link;
        $sql = sprintf("UPDATE tasks_materials SET %s='%s'", $column, $materials["url"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }
    public function add_file($materials)
    {
        global $link;
        $sql = sprintf("INSERT INTO tasks_materials(task_id, file_url) VALUES ('%s', '%s')", $materials["task_id"], $materials["url"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
}
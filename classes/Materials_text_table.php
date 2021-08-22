<?php
require_once __DIR__."/Table.php";


class Materials_text_table implements Table
{

    public function create($material)
    {
        global $link;
        $sql = sprintf("INSERT INTO materials_text(text, task_id) VALUES ('%s','%s')", $material["text"], $material["task_id"]);
        $result = mysqli_query($link, $sql);
        $text_id = mysqli_insert_id($link);
        return $text_id;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM materials_text WHERE task_id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $urls = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $urls;
    }

    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }

    public function delete($material)
    {
        global $link;
        $sql = sprintf("DELETE FROM materials_text WHERE task_id = '%s' AND id = '%s'", $material["task_id"], $material["id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
}
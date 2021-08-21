<?php
require_once __DIR__."/Table.php";


class Materials_docs_url_table implements Table
{

    public function create($material)
    {
        global $link;
        $sql = sprintf("INSERT INTO materials_docs_url(doc_url, task_id, file_name) VALUES ('%s','%s', '%s')", $material["doc_url"], $material["task_id"], $material["file_name"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM materials_docs_url WHERE task_id = '%s'", $id);
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
        $sql = sprintf("DELETE FROM materials_docs_url WHERE task_id = '%s' AND file_name = '%s'", $material["task_id"], $material["file_name"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
}
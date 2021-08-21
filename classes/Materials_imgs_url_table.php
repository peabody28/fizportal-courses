<?php
require_once __DIR__."/Table.php";


class Materials_imgs_url_table implements Table
{

    public function create($material)
    {
        global $link;
        $sql = sprintf("INSERT INTO materials_imgs_url(img_url, task_id) VALUES ('%s','%s')", $material["img_url"], $material["task_id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM materials_imgs_url WHERE task_id = '%s'", $id);
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
        $sql = sprintf("DELETE FROM materials_imgs_url WHERE task_id = '%s' AND img_url = '%s'", $material["task_id"], $material["img_url"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
}
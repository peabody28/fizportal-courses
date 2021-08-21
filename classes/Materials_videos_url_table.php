<?php
require_once __DIR__."/Table.php";


class Materials_videos_url_table implements Table
{

    public function create($material)
    {
        global $link;
        $sql = sprintf("INSERT INTO materials_videos_url(video_url, task_id) VALUES ('%s','%s')", $material["video_url"], $material["task_id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM materials_videos_url WHERE task_id = '%s'", $id);
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
        $sql = sprintf("DELETE FROM materials_videos_url WHERE task_id = '%s' AND video_url = '%s'", $material["task_id"], $material["video_url"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }
}
<?php
require_once __DIR__."/Table.php";


class Themes_points_limit_table implements Table
{

    public function create($obj)
    {
        global $link;
        $sql = sprintf("INSERT INTO themes_points_limit(theme_id, points_limit) VALUES ('%s', '%s')", $obj["theme_id"], $obj["points_limit"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($theme_id)
    {
        global $link;
        $sql = sprintf("SELECT points_limit FROM themes_points_limit WHERE theme_id='%s'", $theme_id);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
    }

    public function update($obj, $column)
    {
        if ($this->read($obj["theme_id"]))
        {
            global $link;
            $sql = sprintf("UPDATE themes_points_limit SET %s='%s' WHERE theme_id='%s'",$column, $obj["points_limit"], $obj["theme_id"]);
            $result = mysqli_query($link, $sql);
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            return $row;
        }
        else
            $this->create($obj);
    }

    public function delete($obj)
    {
    }

}
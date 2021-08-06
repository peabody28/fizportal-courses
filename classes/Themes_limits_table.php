<?php
require_once __DIR__."/Table.php";


class Themes_limits_table implements Table
{

    public function create($obj)
    {
        global $link;
        $sql = sprintf("INSERT INTO themes_limits(theme_id, time_limit) VALUES ('%s', '%s')", $obj["theme_id"], $obj["time_limit"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($theme_id)
    {
        global $link;
        $sql = sprintf("SELECT time_limit FROM themes_limits WHERE theme_id='%s'", $theme_id);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
    }

    public function update($obj, $column)
    {
       if ($this->read($obj["theme_id"]))
       {
           global $link;
           $sql = sprintf("UPDATE themes_limits SET %s = '%s' WHERE theme_id='%s'", $column, $obj["time_limit"], $obj["theme_id"]);
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
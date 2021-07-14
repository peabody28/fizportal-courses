<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";

class Users_progress_theme_table implements Table
{

    public function create($obj)
    {
        global $link;
        $sql = sprintf("INSERT INTO users_progress_theme(user_id, theme_id, progress) VALUES ('%s', '%s', '%s')", $obj["user_id"], $obj["theme_id"], $obj["progress"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function read($obj)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users_progress_theme WHERE user_id='%s' AND theme_id='%s'", $obj["user_id"], $obj["theme_id"]);
        $result = mysqli_query($link, $sql);
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $row;
    }

    public function update($obj, $code)
    {
        if ($code=="set_point")
        {
            global $link;
            $sql = sprintf("UPDATE users_progress_theme SET progress='%s' WHERE user_id='%s' AND theme_id='%s'", $obj["progress"], $obj["user_id"], $obj["theme_id"]);
            $result = mysqli_query($link, $sql);
            return $result;
        }
        else
            return false;

    }

    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }

    public function add_point($target)
    {
        $row = $this->read($target);
        if(!$row){
            $target["progress"]='1';
            $this->create($target);
        }
        else
        {
            $target["progress"]=(string)((int)$row["progress"]+1);
            $this->update($target, "set_point");
        }
    }
    public function delete_point($target)
    {
        $row = $this->read($target);
        if(!$row){
            $target["progress"]='-1';
            $this->create($target);
        }
        else
        {
            $target["progress"]=(string)((int)$row["progress"]-1);
            $this->update($target, "set_point");
        }
    }
}
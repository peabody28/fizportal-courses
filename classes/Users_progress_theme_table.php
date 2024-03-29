<?php
require_once __DIR__."/Table.php";


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
        $sql = sprintf("SELECT progress FROM users_progress_theme WHERE user_id='%s' AND theme_id='%s'", $obj["user_id"], $obj["theme_id"]);
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
        global $link;
        $sql = sprintf("DELETE FROM users_progress_theme WHERE user_id='%s' AND theme_id='%s'", $obj["user_id"], $obj["theme_id"]);
        $result = mysqli_query($link, $sql);
        return $result;
    }

    public function add_point($target, $count=1)
    {
        $row = $this->read($target);
        if(!$row){
            $target["progress"]=(string)$count;
            $this->create($target);
        }
        else
        {
            $target["progress"]=(string)((int)$row["progress"]+(int)$count);
            $this->update($target, "set_point");
        }
    }
    public function delete_point($target, $count=1)
    {
        $row = $this->read($target);
        if(!$row){
            $target["progress"]=(string)($count*(-1));
            $this->create($target);
        }
        else
        {
            $target["progress"]=(string)((int)$row["progress"]-(int)$count);
            $this->update($target, "set_point");
        }
    }
}
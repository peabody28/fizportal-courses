<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";


class Themes_table implements Table
{

    public function create($theme)
    {
        global $link;
        $sql = sprintf("INSERT INTO themes(title, text, complexity, course_id) VALUES ('%s', '%s', '%s', '%s')", $theme->title, $theme->text, $theme->complexity, $theme->course_id);
        $result = mysqli_query($link, $sql);
        $theme->id = mysqli_insert_id($link);
        return $theme->id ? true: false;
    }
    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM themes WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $theme_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $theme_data;
    }
    public function update($theme, $column)
    {
        global $link;
        $sql = sprintf("UPDATE themes SET %s='%s' WHERE id = '%s'", $column, $theme->$column, $theme->id);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function delete($id)
    {
        global $link;
        $sql = sprintf("DELETE FROM themes WHERE id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function get_courses_themes($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM themes WHERE course_id='%s'", $id);
        $result = mysqli_query($link, $sql);
        $themes_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $themes_list;
    }
}
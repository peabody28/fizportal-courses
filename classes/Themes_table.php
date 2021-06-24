<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";

class Themes_table implements Table
{

    public function create($theme)
    {
        $row = R::dispense("themes");
        $row->title = $theme->title;
        $row->text = $theme->text;
        $row->course_id = $theme->course_id;
        $row->complexity = $theme->complexity;
        $theme->id = R::store($row);
        return $theme->id ? true:false;
    }
    public function read($theme)
    {
        $row =  R::findOne("themes", "id = ?", [$theme->id]);
        return $row;
    }
    public function update($theme, $column)
    {
        $row =  R::findOne("themes", "id = ?", [$theme->id]);
        $row->$column = $theme->$column;
        $status = R::store($row);
        return $status?true:false;
    }
    public function delete($theme)
    {
        $row =  R::load("themes", $theme->id);
        R::trash($row);
    }
    public function get_themes_course($course)
    {
        $themes_list = array();
        $row = R::findAll("themes", "WHERE course_id=?",[$course->id]);
        foreach ($row as $theme)
            array_push($themes_list, ["id"=>$theme->id, "title"=>$theme->title, "text"=>$theme->text, "complexity"=>$theme->complexity, "course_id"=>$theme->course_id]);
        return $themes_list;
    }
}
<?php
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
        $row = R::load("themes", $theme->id);
        return $row;
    }
    public function update($obj, $code)
    {
        // TODO: Implement update() method.
    }
    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }

    public function get_themes_course($id)
    {
        $themes_list = array();
        $row = R::findAll("themes", "WHERE course_id=?",[$id]);
        foreach ($row as $theme)
            array_push($themes_list, ["id"=>$theme->id, "title"=>$theme->title, "text"=>$theme->text, "complexity"=>$theme->complexity, "course_id"=>$theme->course_id]);
        return $themes_list;
    }
}
<?php
require_once __DIR__."/Table.php";

class Themes_table implements Table
{

    public function create($theme)
    {
        R::selectDatabase("courses_list");
        $row = R::dispense("themes");
        $row->name = $theme->name;
        $row->tasks = json_encode([]);
        $row->level = $theme->level;
        $theme->id = R::store($row);
        R::selectDatabase("default");
        return $theme->id ? true:false;
    }
    public function read($theme)
    {
        R::selectDatabase("courses_list");
        $row = R::load("themes", $theme->id);
        R::selectDatabase("default");
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
}
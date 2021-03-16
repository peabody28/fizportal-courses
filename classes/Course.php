<?php

class Course
{
    public $id, $name, $title, $themes;

    function __construct($name=NULL, $title=NULL)
    {
        $this->name = $name;
        $this->title = $title;
    }

    function add()
    {
        $cours = R::dispense("courses");
        $cours->name = $this->name;
        $cours->title = $this->title;
        $this->id = R::store($cours);
        if ($this->id)
            return json_encode(["status"=>"OK"]);
    }
    function search($id)
    {
        return R::findOne("courses", $id);
    }
    function remove()
    {
        //мОЖЕТ УДАЛЯТЬ ПО ИМЕНИ?
        $cours = R::load("courses", $this->id);
        $status = R::trash($cours);
        if ($status)
            return json_encode(["status"=>"OK"]);
    }
}

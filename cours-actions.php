<?php
require_once "db.php";

$cours_data = $_POST;
if (isset($cours_data["submit"]))
{
    R::selectDatabase("courses_list");
    if ($cours_data["code"]=="add")
    {

        $cours = R::dispense("courses");
        $cours->name = $cours_data["cours_name"];
        $cours->title = $cours_data["title"];
        $id = R::store($cours);
        if ($id)
            echo json_encode(["status"=>"OK"]);

    }
    else if($cours_data["code"]=="rm")
    {
        $cours = R::load("courses", $cours_data["id"]);
        $id = R::trash($cours);
        if ($id)
            echo json_encode(["status"=>"OK"]);
    }
    R::selectDatabase("default");
}
else
    echo "СЮДА НЕЛЬЗЯ ПОПАСТЬ";


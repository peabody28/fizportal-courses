<?php
session_start();

$data = $_POST;
if(isset($data["submit"]))
{
    session_destroy();
    echo json_encode(["status"=>"OK"]);
}
else
    echo "Что ты тут забыл?";

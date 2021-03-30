<?php
require_once __DIR__."/classes/User_session.php";

$data = $_POST;
if(isset($data["submit"]))
{
    $session = new User_session();
    $session->delete();
    echo json_encode(["status"=>"OK"]);
}
else
    echo "Что ты тут забыл?";

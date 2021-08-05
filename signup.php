<?php
require_once __DIR__."/classes/User.php";

$data = $_POST;

if (isset($data["submit"]))
{
    $user = new User();
    $response = $user->signup($data);
    echo json_encode($response);
}
else
{
    echo file_get_contents("templates/signup.html");
}

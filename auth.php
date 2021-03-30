<?php
session_start();
require_once __DIR__."/classes/User.php";
if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {
        $user = new User();
        $user->id = $_COOKIE["id"];
        $user->get_user();

        if($user->hash == $_COOKIE["hash"])
        {
            $_SESSION["name"]=$user->name;
            $_SESSION["rights"]=$user->rights;
        }
        else
            header("Location: /index.php");
    }
    else
        header("Location: /index.php");
}




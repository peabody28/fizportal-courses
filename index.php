<?php
session_start();
require_once __DIR__."/classes/User.php";
if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {
        $user = new User();
        $user->id = $_COOKIE["id"];
        $user->get();

        if($user->hash == $_COOKIE["hash"])
        {
            $_SESSION["name"]=$user->name;
            $_SESSION["rights"]=$user->rights;
            header("Location: /main.php");
        }
        else
            header("Location: /login.php");
    }
    else
        header("Location: /login.php");
}
else
    header("Location: /main.php");

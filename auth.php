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
        // сравниваю хэш из куки и хеш в таблице
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




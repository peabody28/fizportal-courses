<?php
session_start();
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {
        $user = new User();
        $user->id = $_COOKIE["id"];
        $users_table = new Users_table();
        $db_user = $users_table->read($user);
        $user->name = $db_user->name;
        $user->password = $db_user->password;
        $user->hash = $db_user->hash;

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

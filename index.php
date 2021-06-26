<?php
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/User_session.php";
require_once __DIR__."/classes/Users_table.php";
session_start();


if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {

        $users_table = new Users_table();
        $db_user = $users_table->read($_COOKIE["id"]);
        if($db_user)
        {
            $user = new User();
            $user->id = $db_user["id"];
            $user->name = $db_user["name"];
            $user->rights= $db_user["rights"];
            $user->hash = $db_user["hash"];

            if($user->hash == $_COOKIE["hash"])
            {
                $_SESSION["id"]=$user->id;
                $_SESSION["name"]=$user->name;
                $_SESSION["rights"]=$user->rights;
                header("Location: /main.php");
            }
            else
                header("Location: /login.php");
        }
        else
        {
            $sess = new User_session();
            $sess->delete();
            header("Location: /login.php");
        }

    }
    else
        header("Location: /login.php");
}
else
    header("Location: /main.php");

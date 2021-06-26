<?php
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
session_start();


if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {
        // получаю инфу пользователя
        $users_table = new Users_table();
        $tmp_user = $users_table->read($_COOKIE["id"]);
        if(!$tmp_user)
            header("Location: /signup.php");
        $user = new User();
        $user->id = $tmp_user["id"];
        $user->hash = $tmp_user["hash"];
        $user->rights = $tmp_user["rights"];
        // сравниваю хэш из куки и хеш в таблице
        if($user->hash == $_COOKIE["hash"])
        {
            $_SESSION["id"]=$user->id;
            $_SESSION["name"]=$user->name;
            $_SESSION["rights"]=$user->rights;
        }
        else
            header("Location: /login.php");
    }
    else
        header("Location: /login.php");
}
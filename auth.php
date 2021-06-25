<?php
session_start();
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Users_table.php";
if(!isset($_SESSION["name"]))
{
    if(isset($_COOKIE["id"]))
    {
        //создаю объект пользователя
        $user = new User();
        $user->id = $_COOKIE["id"];
        // получаю инфу пользователя
        $users_table = new Users_table();
        $tmp_user = $users_table->read($user);
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
            header("Location: /index.php");
    }
    else
        header("Location: /index.php");
}
<?php
session_start();
// работа с сессией
class User_session
{
    public function create(User $user)
    {
        $_SESSION["name"]   = $user->name;
        $_SESSION["rights"] = $user->rights;
        if($user->create_cookie)
        {
            setcookie("id", $user->id, time() + 7*24*3600, "/");
            setcookie("hash", $user->hash, time() + 7*24*3600, "/");
        }
    }
    public function delete()
    {
        setcookie("id", "", time() -1);
        setcookie("hash", "", time() -1);
        session_destroy();
    }
}
<?php
session_start();
// работа с сессией
class User_session
{
    public function create_session(User $user)
    {
        $_SESSION["id"]   = $user->id;
        $_SESSION["name"]   = $user->name;
        $_SESSION["rights"] = $user->rights;
    }
    public function create_cookie(User $user)
    {
        setcookie("id", $user->id, time() + 7*24*3600, "/");
        setcookie("hash", $user->hash, time() + 7*24*3600, "/");
    }
    public function delete()
    {
        setcookie("id", "", time() -1);
        setcookie("hash", "", time() -1);
        session_destroy();
    }
    public function generate_code($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
}
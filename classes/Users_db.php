<?php
require_once "db.php";

// взаимодействие непосредственно с базой
class Users_db
{
    public function add(User $user): array
    {
        $row = R::dispense("users");
        $row->name = $user->name;
        $row->password = md5(md5($user->password));
        $row->rights = $user->rights;
        $row->hash = $user->hash;
        $id = R::store($row);
        return $id?["status"=>"OK"]:["status"=>"ERROR", "error"=>"not work("];
    }
    public function search_user(User $user): array
    {
        $row = R::findOne("users", "WHERE name = ?", [$user->name]);
        if($row)
        {
            if($row->password == md5(md5($user->password)))
            {
                // дополняю обьект данными из БД
                $user->id = $row->id;
                $user->rights = $row->rights;
                $user->hash = $row->hash;
                return ["status" => "OK"];
            }
            else
                return ["status"=>"ERROR", "error"=>"Неверный пароль"];
        }
        else
            return ["status"=>"ERROR", "error"=>"Неверное имя"];
    }
    public function check_existence_username(User $user)
    {
        $row = R::findOne("users", "WHERE name = ?", [$user->name]);
        return $row?true:false;
    }
    public function read(User $user)
    {
        $row = R::load("users", $user->id);
        if($row)
        {
            $user->name = $row->name;
            $user->password = $row->password;
            $user->rights = $row->rights;
            $user->hash = $row->hash;
            $user->existence = true;
        }
    }
    public function update_hash(User $user)
    {
        $row = R::load("users", $user->id);
        $row->hash = $user->hash;
        R::store($row);
    }
}
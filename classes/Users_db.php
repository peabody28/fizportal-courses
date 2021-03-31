<?php
require_once __DIR__."/../db.php";

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
        $user->id = R::store($row);
        return $user->id?["status"=>"OK"]:["status"=>"ERROR", "error"=>"not work("];
    }
    public function search_user(User $user)
    {
        $row = R::findOne("users", "WHERE name = ?", [$user->name]);
        return $row;
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
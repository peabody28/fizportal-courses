<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";

// взаимодействие непосредственно с базой
class Users_table implements Table
{
    public function create($user): bool
    {
        $row = R::dispense("users");
        $row->name = $user->name;
        $row->password = md5(md5($user->password));
        $row->hash = $user->hash;
        $user->id = R::store($row);
        return $user->id?true:false;
    }
    public function read($user)
    {
        return R::load("users", $user->id);
    }
    public function update($user, $code)
    {
        $row = R::load("users", $user->id);
        if($code == "name")
            $row->name = $user->name;
        if($code == "password")
            $row->password = md5(md5($user->password));
        if($code == "hash")
            $row->hash = $user->hash;
        R::store($row);
    }
    public function delete($user){
        // TODO: deliting user
    }
    public function check_existence_username(User $user)
    {
        $row = R::findOne("users", "WHERE name = ?", [$user->name]);
        return $row?($row->id):false;
    }

}
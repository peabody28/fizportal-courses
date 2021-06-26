<?php
require_once __DIR__."/Table.php";


$link = mysqli_connect("127.0.0.1", "root", "1234", "fizportal_courses");


// взаимодействие непосредственно с базой
class Users_table implements Table
{
    public function create($user): bool
    {
        global $link;
        $sql = sprintf("INSERT INTO users(name, password, hash) VALUES ('%s', '%s', '%s')", $user->name, md5(md5($user->password)), $user->hash );
        $result = mysqli_query($link, $sql);
        $user->id = mysqli_insert_id($link);
        return $result;
    }
    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users WHERE id = '%s'", $id );
        $result = mysqli_query($link, $sql);
        $user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $user_data;
    }
    public function update($user, $code)
    {
        global $link;
        $sql = sprintf("UPDATE users SET %s='%s' WHERE id='%s'", $code, $user->$code, $user->id);
        $result = mysqli_query($link, $sql);
        return $result;
    }
    public function delete($id){
        // TODO: deliting user
    }
    public function check_existence_username($name)
    {
        global $link;
        $sql = sprintf("SELECT * FROM users WHERE name = '%s'", $name);
        $result = mysqli_query($link, $sql);
        $user_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $user_data;
    }

}
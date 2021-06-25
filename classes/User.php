<?php
require_once __DIR__ . "/../classes/Users_table.php";


class User
{
    public $id, $name, $password, $rights, $hash=null;

    public function get()
    {
        $users_table = new Users_table();
        $user = $users_table->read($this->id);
        if($user)
        {
            $this->name = $user["name"];
            $this->password = $user["password"];
            $this->rights = $user["rights"];
            $this->hash = $user["hash"];
        }
    }
}
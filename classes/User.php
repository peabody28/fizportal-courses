<?php
require_once __DIR__."/Users_table.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_themes_table.php";
require_once __DIR__."/Users_themes_time_table.php";




class User
{
    public $id, $name, $email, $rights, $hash=null, $password;

    public function __construct($id=null)
    {
        if($id !== null)
        {
            $users_table = new Users_table();
            $tmp_user = $users_table->read($id);
            $this->id = $tmp_user["id"];
            $this->name = $tmp_user["name"];
            $this->email = $tmp_user["email"];
            $this->password = $tmp_user["password"];
            $this->rights = $tmp_user["rights"];
            $this->hash = $tmp_user["hash"];
        }

    }

}
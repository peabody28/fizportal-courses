<?php
require_once __DIR__."/Users_table.php";
require_once __DIR__."/Users_tasks_table.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Users_themes_table.php";




class User
{
    public $id, $name, $rights, $hash=null;
    private $password;

    public function __construct($id)
    {
        $users_table = new Users_table();
        $tmp_user = $users_table->read($id);
        $this->id = $tmp_user["id"];
        $this->name = $tmp_user["name"];
        $this->password = $tmp_user["password"];
        $this->rights = $tmp_user["rights"];
        $this->hash = $tmp_user["hash"];
    }

    public function get_tasks()
    {
        $users_tasks_table = new Users_tasks_table();
        $users_tasks = $users_tasks_table->get_users_tasks($this->id);
        return $users_tasks;
    }

    public function get_mistakes()
    {
        $users_mistakes_table = new Users_mistakes_table();
        $users_mistakes = $users_mistakes_table->read($this->id);
        return $users_mistakes;
    }

    public function get_progress_theme($id)
    {
        $users_progress_theme_table = new Users_progress_theme_table();
        $users_progress = $users_progress_theme_table->read(["user_id"=>$this->id, "theme_id"=>$id]);
        return (int)$users_progress["progress"];
    }

    public function get_themes()
    {
        $users_themes_table = new Users_themes_table();
        $users_themes_list = $users_themes_table->read($this->id);
        return $users_themes_list;
    }

}
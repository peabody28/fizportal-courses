<?php
require_once __DIR__."/Supertests_table.php";


class Supertest
{
    public $id, $theme_id;

    public function __construct($theme_id=null)
    {
        if($theme_id!==null)
        {
            $supertests_table = new Supertests_table();
            $tmp_sptest = $supertests_table->read_by_theme($theme_id);
            $this->id = $tmp_sptest["id"];
            $this->theme_id = $tmp_sptest["theme_id"];
        }
    }
}
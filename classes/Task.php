<?php
require_once __DIR__ . "/Tasks_table.php";


class Task
{
    public $id, $text, $answer=null, $complexity=0, $theme_id=0, $type=null, $img_url=null;
}
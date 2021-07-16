<?php
require_once __DIR__."/../db_connect.php";


interface Table
{
    public function create($obj);
    public function read($obj);
    public function update($obj, $column);
    public function delete($obj);
}
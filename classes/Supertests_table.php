<?php
require_once __DIR__."/Table.php";
require_once __DIR__."/../db_connect.php";


class Supertests_table implements Table
{

    public function create($theme_id)
    {
        global $link;
        $sql = sprintf("INSERT INTO supertests(theme_id) VALUES ('%s')", $theme_id);
        $result = mysqli_query($link, $sql);
        return $result ? true: false;
    }

    public function read($theme_id)
    {
        global $link;
        $sql = sprintf("SELECT * FROM supertests WHERE theme_id = '%s'", $theme_id);
        $result = mysqli_query($link, $sql);
        $supertest_data = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return $supertest_data;
    }

    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }

    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }
}
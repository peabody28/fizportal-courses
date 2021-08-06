<?php
require_once __DIR__."/Table.php";


class Supertests_tasks_table implements Table
{

    public function create($supertest)
    {
        global $link;
        $sql = sprintf("SELECT * FROM supertests_tasks WHERE supertest_id='%s' AND task_id='%s'", $supertest["id"], $supertest["task_id"]);
        $res = mysqli_query($link, $sql);
        if($res->num_rows)
            return false;
        else
        {
            $sql = sprintf("INSERT INTO supertests_tasks(supertest_id, task_id) VALUES ('%s', '%s')", $supertest["id"], $supertest["task_id"]);
            $result = mysqli_query($link, $sql);
        }
        return $result;
    }

    public function read($id)
    {
        global $link;
        $sql = sprintf("SELECT task_id FROM supertests_tasks WHERE supertest_id = '%s'", $id);
        $result = mysqli_query($link, $sql);
        $supertest_tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $supertest_tasks;
    }

    public function update($obj, $column)
    {
    }

    public function delete($obj)
    {
    }
}
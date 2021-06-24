<?php
require_once __DIR__."/../db.php";
require_once __DIR__."/Table.php";

class Tasks_table implements Table
{

    public function create($task)
    {
        $row = R::dispense("tasks");
        $row->text = $task->text;
        $row->answer = $task->answer;
        $row->complexity = $task->complexity;
        $row->image_url = $task->image_url;
        $row->theme_id = $task->theme_id;
        $task->id = R::store($row);
        return $task->id;
    }

    public function read($task)
    {
        $row =  R::findOne("tasks", "id = ?", [$task->id]);
        return $row;
    }

    public function update($obj, $column)
    {
        // TODO: Implement update() method.
    }

    public function delete($obj)
    {
        // TODO: Implement delete() method.
    }

    public function get_tasks_theme($theme)
    {
        $tasks_list = array();
        $row = R::findAll("tasks", "WHERE theme_id=?",[$theme->id]);
        foreach ($row as $task)
            array_push($tasks_list, ["id"=>$task->id, "answer"=>$task->answer, "text"=>$task->text, "image_url"=>$task->image_url, "complexity"=>$task->complexity, "theme_id"=>$task->theme_id]);
        return $tasks_list;
    }
}
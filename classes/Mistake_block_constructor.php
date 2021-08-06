<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Tasks_block_constructor.php";
require_once __DIR__."/Render.php";


class Mistake_block_constructor extends Tasks_block_constructor
{
    public function get_mistake_block($id, $next_id=null)
    {
        $task = new Task($id);

        $block = new Render();

        $task_block = $block->render_mistake($task, $next_id);
        // материалы для задачи
        $task_block .= "<div class='h2 d-flex justify-content-center col-12 mt-3' id='message'></div>";
        $task_block .= "<div class='col-12 mt-5 d-flex justify-content-center'> <a href='/materials?task_id=$task->id'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];
    }
}
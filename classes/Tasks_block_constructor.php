<?php
require_once __DIR__."/Task.php";
require_once __DIR__."/Tasks_table.php";
require_once __DIR__."/Themes_table.php";
require_once __DIR__."/Themes_points_limit_table.php";
require_once __DIR__."/Render.php";
require_once __DIR__."/Users_progress_theme_table.php";
require_once __DIR__."/Supertests_tasks_table.php";
require_once __DIR__."/Professor.php";



class Tasks_block_constructor
{
    public function get_text_theme_block($id)
    {
        $themes_table = new Themes_table();
        $tmp_theme = $themes_table->read($id);

        $block = "<div class='col-12 m-0 p-0 d-flex justify-content-center'>Описание темы</div><div class='col-12 m-0 p-0 text-break'>$tmp_theme[text]</div>";
        return ["block"=>$block];

    }

    public function get_task_block($id, $next_id=null, $is_admin=false)
    {
        $task = new Task($id);

        $block = new Render();

        $task_block = $block->render_task($task, $next_id, 0);
        if ($is_admin)
        {
            $task_block .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task->id'>Изменить задачу</a></div>";
            $task_block .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task->id);return false;'>Удалить эту задачу</button>
                             </div>";
        }
        // материалы для задачи
        $task_block .= "<div class='d-flex justify-content-center col-12 mt-3' id='message'></div>";
        $task_block .= "<div class='col-12 d-flex justify-content-center'> <a href='/materials?task_id=$task->id'>Материалы для задачи</a></div>";
        return ["block"=>$task_block];

    }
}

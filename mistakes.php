<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/classes/Render.php";
require_once __DIR__ . "/classes/Themes_table.php";
require_once __DIR__ . "/classes/Users_mistakes_table.php";
require_once __DIR__ . "/classes/Tasks_table.php";
require_once __DIR__ . "/classes/Professor.php";
session_start();


if (isset($_GET["theme_id"])) {

    $themes_table = new Themes_table();
    if($themes_table->read($_GET["theme_id"]))
    {
        $professor = new Professor();
        // можно ли пользователю решать эту РО?
        if($professor->mistakes_status($_GET["theme_id"]))
        {
            // нахожу ошибки пользователя для этой темы
            $users_mistakes_table = new Users_mistakes_table();
            $all_mistakes = $users_mistakes_table->read($_SESSION["id"]);

            $tasks_table = new Tasks_table();
            $tasks_theme = $tasks_table->get_tasks_theme($_GET["theme_id"]);

            $mistakes = [];
            foreach ($tasks_theme as $tt)
                if(in_array(["task_id"=>$tt["id"]], $all_mistakes))
                    $mistakes[] = $tt;

            if (count($mistakes))
            {
                $content = "";
                $render = new Render();
                $content .= $render->render_mistakes($mistakes);

                $content .= "<div id='task' class='p-0 m-0 mt-5 pt-5 d-flex justify-content-center align-items-center row container-fluid'><div class='tt p-0 m-0 row container-fluid d-flex justify-content-center'></div></div>";
                $page = new Render();
                $page->temp = 'main.html';
                $page->argv = ['title' => "mistakes",
                    'css' => "/css/mistakes.css",
                    "name" => "<h2>$_SESSION[name]</h2>",
                    "content" => $content,
                    "js" => "/js/mistakes.js"
                ];
                echo $page->render_temp();
            }
            else
                header("Location: /acc");
        }
        else
            header("Location: /acc");
    }
    else
        header("Location: /acc");
} else
    header("Location: /acc");




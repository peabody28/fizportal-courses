<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/classes/User.php";
require_once __DIR__ . "/classes/Theme.php";
require_once __DIR__ . "/classes/Render.php";
require_once __DIR__ . "/classes/Themes_table.php";
require_once __DIR__ . "/classes/Users_mistakes_table.php";
require_once __DIR__ . "/classes/Tasks_table.php";
require_once __DIR__ . "/classes/Professor_mistakes.php";
session_start();


if (isset($_GET["theme_id"]))
{

    $theme = new Theme($_GET["theme_id"]);
    if($theme->id)
    {
        $user = new User($_SESSION["id"]);
        $prof_mist = new Professor_mistakes();
        // можно ли пользователю решать эту РО?
        if($prof_mist->mistakes_status($user, $theme))
        {
            // нахожу ошибки пользователя для этой темы
            $all_mistakes = $prof_mist->get_mistakes($user);

            $tasks_theme = $theme->get_tasks();
            $tasks_theme_ids = [];
            foreach ($tasks_theme as $tt)
                $tasks_theme_ids[] = $tt->id;

            $mistakes = [];
            foreach ($all_mistakes as $mistake)
            {
                if(in_array($mistake->id, $tasks_theme_ids))
                    $mistakes[] = $mistake;
            }

            if (count($mistakes))
            {
                $content = "";
                $render = new Render();
                $content .= $render->render_mistakes($mistakes);

                $content .="<div id='task' class='p-0 m-0 mt-5 pt-md-5 d-flex justify-content-center align-items-center row container-fluid'>
                                <div id='tt' class='p-4 pt-5 m-0 ml-md-5 mr-md-5 row container-fluid d-flex justify-content-center'>";

                $this_task = $mistakes[0];

                $response = $this_task->get_html(["is_admin"=>($user->rights == "admin")]);
                $content .= $response["block"];
                $content.=" </div></div>";// закрыл #tt и #task

                $page = new Render();
                $page->temp = 'main.html';
                $page->argv = ['title' => "mistakes",
                    'css' => "/css/mistakes.css",
                    "name" => "<h2>$user->name</h2>",
                    "content" => $content,
                    "js" => "/js/mistakes.js",
                    "mathjax"=>file_get_contents("templates/mathjax.html")
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
}
else
    header("Location: /acc");




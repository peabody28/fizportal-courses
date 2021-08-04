<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Users_mistakes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
session_start();


$content = "";
$content .= "<div class='row w-100 p-0 m-0 justify-content-start'><a id='exit' class='btn btn-md' href='/exit'>Выйти</a></div><br>";

//if($_SESSION["rights"]=="admin")
   // $content.= "<div class='row w-100 p-2 m-0 justify-content-start'><a class='btn adm_btn' href='/admin_page'>Админка</a></div>";

$users_themes_table = new Users_themes_table();
$users_themes = $users_themes_table->read($_SESSION["id"]);

$professor = new Professor();
foreach ($users_themes as $item) {
    if($professor->mistakes_status($item["theme_id"]))
    {
        // список всех ошибок пользователя
        $users_mistakes_table = new Users_mistakes_table();
        $all_mistakes = $users_mistakes_table->read($_SESSION["id"]);
        // список задач данной темы
        $tasks_table = new Tasks_table();
        $tasks_theme = $tasks_table->get_tasks_theme($item["theme_id"]);

        $mistakes = []; // ошибки пользователя в данной теме
        foreach ($tasks_theme as $tt)
        {
            if(in_array(["user_id"=>$_SESSION["id"], "task_id"=>$tt["id"]], $all_mistakes))
                $mistakes[] = $tt;
        }

        if(count($mistakes)) // если в теме есть ошибки
            $content .= "<a class='btn ro' href='/mistakes?theme_id=$item[theme_id]'>Работа над ошибками для темы $item[theme_id]</a><br>";
    }

}

$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"acc",
    'css'=>"/css/acc.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "disabled_$file"=>"disabled",
    "js"=>"/js/acc.js",
    "mathjax"=>file_get_contents("templates/mathjax.html")
] ;

echo $page->render_temp();




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Professor_mistakes.php";
require_once __DIR__."/classes/Users_themes_table.php";
require_once __DIR__."/classes/Users_mistakes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
session_start();


$content = "";
$content .= "<div class='row w-100 p-0 m-0 justify-content-start'><a id='exit' class='btn btn-md' href='/exit'>Выйти</a></div><br>";

//if($_SESSION["rights"]=="admin")
   // $content.= "<div class='row w-100 p-2 m-0 justify-content-start'><a class='btn adm_btn' href='/admin_page'>Админка</a></div>";

$user = new User($_SESSION["id"]);
$professor = new Professor();
$users_themes = $professor->get_themes($user);


$prof_mist = new Professor_mistakes();
foreach ($users_themes as $item) {
    if($prof_mist->mistakes_status($item->id))
    {
        // список всех ошибок пользователя
        $all_mistakes = $prof_mist->get_mistakes($user);
        $all_mistakes_ids = [];
        foreach ($all_mistakes as $m)
            $all_mistakes_ids[] = $m->id;

        // список задач данной темы
        $theme = new Theme($item->id);
        $tasks_theme = $theme->get_tasks();

        $mistakes = []; // ошибки пользователя в данной теме
        foreach ($tasks_theme as $tt)
        {
            if(in_array($tt->id, $all_mistakes_ids))
                $mistakes[] = $tt;
        }

        if(count($mistakes)) // если в теме есть ошибки
            $content .= "<a class='btn ro' href='/mistakes?theme_id=$item->id'>Работа над ошибками для темы $item->id</a><br>";
    }

}

$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"acc",
    'css'=>"/css/acc.css",
    "name"=>"<h2>$user->name</h2>",
    "content"=>$content,
    "disabled_$file"=>"disabled",
    "js"=>"/js/acc.js",
    "mathjax"=>file_get_contents("templates/mathjax.html")
] ;

echo $page->render_temp();




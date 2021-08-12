<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Professor.php";
session_start();


$content = "<div class='row w-100 p-0 m-0 justify-content-start'><a id='exit' class='btn btn-md' href='/exit'>Выйти</a></div><br>";

$user = new User();
$user->id = $_SESSION["id"];
$user->rights = $_SESSION["rights"];
$user->name = $_SESSION["name"];

$professor = new Professor();
$professor->student = $user;
$users_themes = $professor->get_themes();


foreach ($users_themes as $theme)
{
    if($professor->mistakes_status($theme))
    {
        // список ошибок пользователя для данной темы
        $mistakes = $professor->get_mistakes_for_theme($theme);
        if(count($mistakes)) // если в теме есть ошибки
            $content .= "<a class='btn ro mb-3' href='/mistakes?theme_id=$theme->id'>Работа над ошибками для темы <i>$theme->title</i></a>";
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




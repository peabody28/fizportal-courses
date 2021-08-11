<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Professor_mistakes.php";
session_start();


$content = "<div class='row w-100 p-0 m-0 justify-content-start'><a id='exit' class='btn btn-md' href='/exit'>Выйти</a></div><br>";

$user = new User($_SESSION["id"]);

$professor = new Professor();
$users_themes = $professor->get_themes($user);


$prof_mist = new Professor_mistakes();
foreach ($users_themes as $theme)
{
    if($prof_mist->mistakes_status($user, $theme))
    {
        // список ошибок пользователя для данной темы
        $mistakes = $prof_mist->get_mistakes_for_theme($user, $theme);
        if(count($mistakes)) // если в теме есть ошибки
            $content .= "<a class='btn ro mb-3' href='/mistakes?theme_id=$theme->id'>Работа над ошибками для темы <i>$theme->title</i>></a>";
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




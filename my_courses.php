<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/classes/User.php";

require_once __DIR__ . "/classes/Manager.php";
require_once __DIR__ . "/classes/Render.php";
session_start();


$data = $_POST;

$user = new User($_SESSION["id"]);

$manager = new Manager();
$users_courses = $manager->get_users_courses($user);

// рендеринг
$content = "<div class='row container-fluid justify-content-center m-0 mb-3 p-0'><h1>Мои курсы</h1></div><br>";

foreach ($users_courses as $course)
    $content .= $course->get_html(["status"=>"open"]);

// чтоб сделать кнопку неактивной
$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "my_courses",
    'css' => "/css/my_courses.css",
    "name" => "<h2>$user->name</h2>",
    "content" => $content,
    "disabled_$file" => "disabled",
    "js" => "/js/my_courses.js",
    "mathjax"=>file_get_contents("templates/mathjax.html")];

echo $page->render_temp();



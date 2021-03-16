<?php
require_once "db.php";
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

$data = $_GET;

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

R::selectDatabase("courses_list");
$courses_list = R::findAll("courses");
R::selectDatabase("default");

$content = "<div id='catalog' class='row container-fluid justify-content-center m-0 mt-5 p-0 h1'><span>Каталог курсов</span></div>";
foreach ($courses_list as $cours)
    $content .= $twig->render("cours-block.html", ["name"=>$cours->name, "title"=>"$cours->title", "id"=>$cours->id]);



$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title'=>"courses_list",
        'css'=>"/css/courses.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "js"=>"/js/courses.js"] );
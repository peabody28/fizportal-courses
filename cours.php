<?php
require_once "db.php";
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

$data = $_GET;

R::selectDatabase("courses_list");
$cours = R::load("courses", $data["id"]);
R::selectDatabase("default");

if ($cours)
    $content = "Тут темы курса \"$cours->name\"";
else
{
    $content = "
    Такого курса нет
    ";
}


$file = basename(__FILE__, ".php");
echo $twig->render('main.html',
    ['title'=>"$cours->name",
        'css'=>"/css/cours.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "js"=>"/js/cours.js"] );


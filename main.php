<?php
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";
session_start();

$form =
    "
    <form id='exit_form'>
        <input type='hidden' name='submit'>
        <input type='submit' value='Выйти'>
    </form>
    ";

$loader = new Twig\Loader\FilesystemLoader(__DIR__.'/templates');
$twig = new Twig\Environment($loader);

echo $twig->render('main.html',
    ['title'=>"main", 'css'=>"/css/main2.css", "content"=>"<h1>Hello $_SESSION[name]</h1><br>$form", "js"=>"/js/ьain.js"] );

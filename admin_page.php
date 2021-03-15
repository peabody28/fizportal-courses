<?php
require_once "auth.php";
require_once __DIR__."/vendor/autoload.php";

if(!isset($_SESSION["rights"]) or !$_SESSION["rights"]=="admin")
{
    echo "you not admin";
}
else {

    $content =
        "
        ДОБАВИТЬ КУРС
        <form class='cours-doing' method='POST'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='code' value='add'>
            <input type='text' name='cours_name'>
            <input type='text' name='title'>
            <button type='submit' class='btn btn-md'>Создать</button>
        </form>
        <br>
        <br>
        УДАЛИТЬ КУРС
        <form class='cours-doing' method='POST'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='code' value='rm'>
            <input type='text' name='id'>
            <button type='submit' class='btn btn-md'>Удалить</button>
        </form>
        <div id='resp'></div>
        ";

    $loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
    $twig = new Twig\Environment($loader);

    $file = basename(__FILE__, ".php");
    echo $twig->render('main.html',
        ['title' => "main",
            'css' => "/css/main.css",
            "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
            "content" => $content,
            "disabled_$file" => "",
            "js" => "/js/admin_page.js"]);
}


<?php
require_once __DIR__."/db.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";


$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "main",
    'css' => "/css/main.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => "this is admin-page",
    "js" => "/js/admin_page.js"];

echo $page->render_page();

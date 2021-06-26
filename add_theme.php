<?php
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;
if(isset($data["submit"]))
{
    $theme = new Theme();
    $theme->title = $data["theme_title"];
    $theme->text = $data["theme_text"];
    $theme->course_id = $data["course_id"];
    $theme->complexity = $data["theme_complexity"];

    // TODO: ВОЗМОЖНО ЗДЕСЬ НУЖНА ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ
    $themes_table = new Themes_table();
    $response = $themes_table->create($theme);
    echo json_encode(["course_id"=>$data["course_id"]]);
}
else
{
    $add_theme_block = new Render();
    $add_theme_block->temp = "add_theme_form.html";
    $add_theme_block->argv = ["course_id"=>$_GET["course_id"]];
    $content = $add_theme_block->render_temp();

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "add_theme",
        'css' => "/css/add_theme.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/add_theme.js"];
    echo $page->render_temp();
}



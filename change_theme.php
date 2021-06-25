<?php
session_start();
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Render.php";

$data = $_POST;
if(isset($data["submit"]))
{
    $theme = new Theme();
    $theme->id = $data["theme_id"];

    if($data["code"]=="delete")
    {
        $themes_table = new Themes_table();
        $themes_table->delete($theme->id);
        echo json_encode(["status"=>"OK"]);
    }
    else if ($data["code"]=="change_title")
    {
        $theme->title = $data["new_theme_title"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "title");
        echo json_encode(["status"=>"OK"]);
    }
    else if ($data["code"]=="change_text")
    {
        $theme->text = $data["new_theme_text"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "text");
        echo json_encode(["status"=>"OK"]);
    }
    else if($data["code"]=="change_complexity")
    {
        $theme->complexity = $data["new_theme_comlexity"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "complexity");
        echo json_encode(["status"=>"OK"]);
    }

}
else
{


    $themes_table = new Themes_table();
    $tmp_theme = $themes_table->read($_GET["id"]);
    if(!$tmp_theme)
        header("Location: /admin_page.php");
    $theme = new Theme();
    $theme->id = $tmp_theme["id"];
    $theme->title = $tmp_theme["title"];
    $theme->text = $tmp_theme["text"];
    $theme->complexity = $tmp_theme["complexity"];
    $theme->course_id = $tmp_theme["course_id"];

    $content = "";
    $forms = new Render();
    $forms->temp = "change_theme_forms.html";
    $forms->argv = ["theme_id"=>$theme->id];
    $content.=$forms->render_temp();
    $content.="<br><br><h2>Задачи темы</h2>";

    //получаю задачи
    $tasks_table = new Tasks_table();
    $tasks_list = $tasks_table->get_tasks_theme($theme->id);
    foreach ($tasks_list as $task) {
        $content .= "<div>$task[text]</div>";
    }
    // поле создания задачи
    $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/add_task.php?theme_id=$theme->id'>Добавить задачу</a> </div><br><br>";
    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_theme",
        'css' => "/css/change_theme.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_theme.js"];
    echo $page->render_temp();

}

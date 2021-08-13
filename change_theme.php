<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Themes_limits_table.php";
require_once __DIR__."/classes/Themes_points_limit_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    $theme = new Theme();
    $theme->id = $data["theme_id"];

    if($data["code"]=="delete")
    {
        $themes_table = new Themes_table();
        $themes_table->delete($theme->id);
        echo json_encode(["status"=>"OK", "code"=>"ch_location"]);
    }
    else if ($data["code"]=="change_title")
    {
        $theme->title = $data["new_theme_title"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "title");
        echo json_encode(["status"=>"OK", "message"=>"Название изменено"]);
    }
    else if ($data["code"]=="change_text")
    {
        $theme->text = $data["new_theme_text"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "text");
        echo json_encode(["status"=>"OK", "message"=>"Описание изменено"]);
    }
    else if($data["code"]=="change_complexity")
    {
        $theme->complexity = $data["new_theme_comlexity"];
        $themes_table = new Themes_table();
        $themes_table->update($theme, "complexity");
        echo json_encode(["status"=>"OK", "message"=>"Сложность изменена"]);
    }
    else if($data["code"]=="change_time_limit")
    {
        $themes_limits_table = new Themes_limits_table();
        $themes_limits_table->update(["theme_id"=>$data["theme_id"], "time_limit"=>$data["new_theme_time_limit"]], "time_limit");
        echo json_encode(["status"=>"OK", "message"=>"Лимит изменен"]);
    }
    else if($data["code"]=="change_points_limit")
    {
        $themes_points_limit_table = new Themes_points_limit_table();
        $themes_points_limit_table->update(["theme_id"=>$data["theme_id"], "points_limit"=>$data["new_theme_points_limit"]], "points_limit");
        echo json_encode(["status"=>"OK", "message"=>"Порог баллов изменен"]);
    }

}
else
{
    $themes_table = new Themes_table();
    $tmp_theme = $themes_table->read($_GET["id"]);
    if(!$tmp_theme)
        header("Location: /courses");
    $theme = new Theme();
    $theme->id = $tmp_theme["id"];
    $theme->title = $tmp_theme["title"];
    $theme->text = $tmp_theme["text"];
    $theme->complexity = $tmp_theme["complexity"];
    $theme->course_id = $tmp_theme["course_id"];
    // беру лимит баллов
    $themes_points_limit_table = new Themes_points_limit_table();
    $resp = $themes_points_limit_table->read($theme->id);
    $limits_of_points = $resp["points_limit"]?:10; // если лимит не установен, принимаем его за 10 баллов
    // беру лимит времени
    $themes_limits_table = new Themes_limits_table();
    $answ = $themes_limits_table->read($theme->id);
    $time_limit = $answ?$answ["time_limit"]:0;
    $time_limit.=" мин";

    $content = "";
    $forms = new Render();
    $forms->temp = "change_theme_forms.html";
    $forms->argv = ["theme_id"=>$theme->id, "course_id"=>$theme->course_id,  "theme_title"=>strip_tags($theme->title), "theme_text"=>strip_tags($theme->text), "theme_complexity"=>$theme->complexity, "points_limit"=>$limits_of_points, "time_limit"=>$time_limit];
    $content.=$forms->render_temp();
    $content.="<br><br><h2>Задачи темы</h2>";

    //получаю задачи
    $tasks_table = new Tasks_table();
    $tasks_list = $tasks_table->get_tasks_theme($theme->id);
    foreach ($tasks_list as $task) {
        $content .= "<div>$task[text]</div>";
    }
    // ссылка на создание задачи
    $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/add_task?theme_id=$theme->id'>Добавить задачу</a> </div><br><br>";

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_theme",
        'css' => "/css/change_theme.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_theme.js"];
    echo $page->render_temp();

}

<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Themes_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    $course = new Course();
    $course->id = $data["course_id"];

    if($data["code"]=="delete")
    {
        $courses_table = new Courses_table();
        $courses_table->delete($course->id);
        echo json_encode(["status"=>"OK"]);
    }
    else if ($data["code"]=="change_title")
    {
        $course->title = $data["new_course_title"];
        $courses_table = new Courses_table();
        $courses_table->update($course, "title");
        echo json_encode(["status"=>"OK"]);
    }
    else if ($data["code"]=="change_text")
    {
        $course->text = $data["new_course_text"];
        $courses_table = new Courses_table();
        $courses_table->update($course, "text");
        echo json_encode(["status"=>"OK"]);
    }
    else if ($data["code"]=="change_price")
    {
        $course->price = $data["new_course_price"];
        $courses_table = new Courses_table();
        $courses_table->update($course, "price");
        echo json_encode(["status"=>"OK"]);
    }
    else if($data["code"]=="change_complexity")
    {
        $course->complexity = $data["new_course_comlexity"];
        $courses_table = new Courses_table();
        $courses_table->update($course, "complexity");
        echo json_encode(["status"=>"OK"]);
    }

}
else
{
    $courses_table = new Courses_table();
    $tmp_course = $courses_table->read($_GET["id"]);
    if(!$tmp_course)
        header("Location: /admin_page.php");
    // беру данные курса из базы
    $course = new Course();
    $course->id = $tmp_course["id"];
    $course->title = $tmp_course["title"];
    $course->text = $tmp_course["text"];
    $course->complexity = $tmp_course["complexity"];
    $course->price = $tmp_course["price"];

    $content = "";
    $forms = new Render();
    $forms->temp = "change_course_forms.html";
    $forms->argv = ["course_id"=>$course->id];
    $content.=$forms->render_temp();
    $content.="<br><br><h2>Темы курса</h2>";

    //получаю темы
    $themes_table = new Themes_table();
    $themes_list = $themes_table->get_courses_themes($course->id);
    // рендеринг тем
    $theme_block = new Render();
    $theme_block->temp = "theme_block_adm.html";
    foreach ($themes_list as $theme) {
        $theme_block->argv = ["title"=>$theme["title"], "id"=>$theme["id"]];
        $content .= $theme_block->render_temp();
    }
    // поле создания темы
    $content.= "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/add_theme.php?course_id=$course->id'>Добавить тему</a> </div><br><br>";
    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_course",
        'css' => "/css/change_course.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_course.js"];
    echo $page->render_temp();

}

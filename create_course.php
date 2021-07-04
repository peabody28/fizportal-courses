<?php
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if (isset($data["submit"]))
{
    $course = new Course();
    $course->title = $data["course_title"];
    $course->text = $data["course_text"];
    $course->price = $data["course_price"];
    $course->complexity = $data["course_comlexity"];
    $course->img_url = $data["course_img_url"];

    // TODO: ВОЗМОЖНО ЗДЕСЬ НУЖНА ПРОВЕРКА ВВЕДЕННЫХ ДАННЫХ
    $courses_table = new Courses_table();
    $response = $courses_table->create($course);
}
else
{
    $content=file_get_contents(__DIR__."/templates/create_course_form.html");
    $content .= "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn back' href='/courses'>Вернуться к списку курсов</a></div><br><br>";
    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "create_course",
        'css' => "/css/create_course.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/create_course.js"];
    echo $page->render_temp();
}

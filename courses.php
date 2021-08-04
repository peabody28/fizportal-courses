<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    // обработка поступления на курс
    $users_courses_table = new Users_courses_table();
    $users_courses_table->create(["user_id"=>$_SESSION["id"], "course_id"=>$data["course_id"]]);
    echo json_encode(["status"=>"OK", "course_id"=>$data["course_id"]]); // редирект к курсу
}
else
{
    // беру список курсов из базы
    $courses_table = new Courses_table();
    $courses_list = $courses_table->get_courses_list();
    // список курсов рользователя
    $users_courses_table = new Users_courses_table();
    $users_courses_list = $users_courses_table->read($_SESSION["id"]);
    //рендеринг
    $content = "";
    if($_SESSION["rights"]=="admin")
        $content = "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_course'>Cоздать курс</a> </div><br><br>";

    $render = new Render();
    for($i = 0; $i<count($courses_list); $i++) {
        $course = $courses_list[$i];

        // от $status зависит рендер курса
        if($_SESSION["rights"]=="admin")
            $status="admin";
        else if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$course["id"]], $users_courses_list))
            $status = "open";
        else
           $status = "close";

        $content .= $render->render_course($course, $status);
    }

    // чтоб сделать кнопку неактивной
    $file = basename(__FILE__, ".php");

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title'=>"courses_list",
        'css'=>"/css/courses.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "disabled_$file"=>"disabled",
        "js"=>"/js/courses.js",
        "mathjax"=>file_get_contents("templates/mathjax.html")] ;

    echo $page->render_temp();
}

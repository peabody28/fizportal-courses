<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Close_course_block.php";
require_once __DIR__."/classes/Course_block.php";
require_once __DIR__."/classes/Users_courses_table.php";
require_once __DIR__."/classes/Users_courses.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;

if(isset($data["submit"]))
{
    // обработка поступления на курс
    $users_courses = new Users_courses();
    $users_courses->user_id = $_SESSION["id"];
    $users_courses->course_id = $data["course_id"];

    $users_courses_table = new Users_courses_table();
    $users_courses_table->create($users_courses);
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
    $close_course_block = new Close_course_block(); // некупленные курсы
    $course_block = new Course_block(); // купленные курсы

    foreach ($courses_list as $course) {
        if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$course["id"]], $users_courses_list)){
            $course_block->argv = ["title" => $course["title"], "text" => $course["text"], "id" => $course["id"]];
            $content .= $course_block->render();
        }
        else {
            $close_course_block->argv = ["title" => $course["title"], "text" => $course["text"], "id" => $course["id"], "price"=>$course["price"]];
            $content .= $close_course_block->render();
        }
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
        "js"=>"/js/courses.js"] ;

    echo $page->render_temp();
}

<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Courses_table.php";
require_once __DIR__."/classes/Course_block_adm.php";
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
    if($_SESSION["rights"]=="admin")
        $content = "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_course'>Cоздать курс</a> </div><br><br>";
    $close_course_block = new Close_course_block(); // некупленные курсы
    $course_block = new Course_block(); // купленные курсы
    $course_block_adm = new Course_block_adm();

    for($i = 0; $i<count($courses_list); $i++) {
        $course = $courses_list[$i];

        if($_SESSION["rights"]=="admin")
        {
            $course_block_adm->argv = ["title" => $course["title"], "text" => $course["text"], "id" => $course["id"], "img_url"=>$course["img_url"]];
            $content .= $course_block_adm->render();
        }
        else if(in_array(["user_id"=>$_SESSION["id"], "course_id"=>$course["id"]], $users_courses_list)){
            $course_block->argv = ["title" => $course["title"], "text" => $course["text"], "id" => $course["id"], "img_url"=>$course["img_url"]];
            $content .= $course_block->render();
        }
        else {
            $close_course_block->argv = ["title" => $course["title"], "text" => $course["text"], "id" => $course["id"], "price"=>$course["price"], "img_url"=>$course["img_url"]];
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

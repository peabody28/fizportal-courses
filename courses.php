<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Manager.php";
require_once __DIR__."/classes/Render.php";
session_start();


$data = $_POST;
$user = new User($_SESSION["id"]);

if(isset($data["submit"]))
{
    // обработка поступления на курс
    $course = new Course($data["course_id"]);

    $manager = new Manager();
    $response = $manager->buy_course($user, $course);
    echo json_encode($response); // редирект к курсу
}
else
{
    // список курсов рользователя
    $manager = new Manager();
    $users_courses = $manager->get_users_courses($user);
    // все курсы
    $all_courses = $manager->get_courses();

    //рендеринг
    $content = "";
    if($user->rights == "admin")
        $content = "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn create' href='/add_course'>Cоздать курс</a> </div><br><br>";

    foreach ($all_courses as $course) {
        // от $status зависит рендер курса
        $response = $manager->check_access_to_course($user, $course->id);
        if($response["status"])
        {
            if ($response["is_admin"])
                $status = "admin";
            else
                $status = "open";
        }
        else
            $status = "close";

        $content .= $course->get_html(["status"=>$status]);
    }

    // чтоб сделать кнопку неактивной
    $file = basename(__FILE__, ".php");

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title'=>"courses_list",
        'css'=>"/css/courses.css",
        "name"=>"<h2>$user->name</h2>",
        "content"=>$content,
        "disabled_$file"=>"disabled",
        "js"=>"/js/courses.js",
        "mathjax"=>file_get_contents("templates/mathjax.html")] ;

    echo $page->render_temp();
}

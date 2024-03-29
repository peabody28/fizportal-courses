<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Courses_table.php";
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
    else if($data["code"]=="change_image")
    {
        $uploaddir = __DIR__.'/media/courses_imgs/';

        $apend= "course".$data["course_id"].'.jpg';

        $uploadfile = "$uploaddir$apend";

        if($_FILES['file']['type'] == 'image/gif' || $_FILES['file']['type'] == 'image/jpeg' || $_FILES['file']['type'] == 'image/png' || $_FILES['file']['type'] == 'image/jpg')
        {
            $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
            if ($status)
            {
                $course->img_url = '/media/courses_imgs/'.$apend;
                $courses_table = new Courses_table();
                $courses_table->update($course, "img_url");
                header("Location: /courses");
            }
            else
                header("Location: /courses");
        }
        else
            header("Location: /courses");

    }

}
else
{
    $courses_table = new Courses_table();
    $tmp_course = $courses_table->read($_GET["id"]);
    if(!$tmp_course)
        header("Location: /courses");
    // беру данные курса из базы
    $course = new Course();
    $course->id = $tmp_course["id"];
    $course->title = $tmp_course["title"];
    $course->text = $tmp_course["text"];
    $course->complexity = $tmp_course["complexity"];
    $course->price = $tmp_course["price"];
    $course->img_url = $tmp_course["img_url"];

    $content = "";
    $forms = new Render();
    $forms->temp = "change_course_forms.html";
    $forms->argv = ["course_id"=>$course->id, "course_title"=>strip_tags($course->title),"course_text"=>strip_tags($course->text), "course_complexity"=>$course->complexity, "course_price"=>$course->price, "course_img_url"=>$course->img_url ];
    $content.=$forms->render_temp();
    $content .= "<br><br><div class='row col-12 p-0 m-0 ml-5 d-flex justify-content-start'><a class='btn back' href='/courses'>Вернуться к списку курсов</a></div><br><br>";
    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title' => "change_course",
        'css' => "/css/change_course.css",
        "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
        "content" => $content,
        "js" => "/js/change_course.js"];
    echo $page->render_temp();

}

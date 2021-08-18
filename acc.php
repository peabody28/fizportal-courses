<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/User.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Theme.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Professor.php";
require_once __DIR__."/classes/Manager.php";
session_start();

function get_stat(Course $course, $user)
{
    $professor = new Professor();
    $professor->student = $user;
    $users_tasks = $professor->get_tasks();

    $count_tasks_in_course = 0;
    $count_users_tasks_in_course = 0;

    $themes = [];
    $open_themes = [];

    $course_themes = $course->get_themes();
    foreach ($course_themes as $theme)
    {
        $count_tasks_in_theme = 0;
        $count_users_tasks_in_theme = 0;

        $tasks_in_theme = $theme->get_tasks();

        foreach ($tasks_in_theme as $theme_task)
        {
            $count_tasks_in_theme++;
            foreach ($users_tasks as $u_task)
            {
                if($theme_task->id == $u_task->id)
                {
                    $count_users_tasks_in_theme++;
                    break;
                }
            }
        }
        $count_tasks_in_course += $count_tasks_in_theme;
        $count_users_tasks_in_course += $count_users_tasks_in_theme;

        $theme_status = $professor->theme_status($theme);
        $theme_status = $theme_status["status"];

        $th["mistakes_href"]=false;
        $th["id"] = $theme->id;
        $th["title"] = $theme->title;
        $th["all_tasks"] = $count_tasks_in_theme;
        $th["tasks"] = $count_users_tasks_in_theme;

        if ($theme_status == "solved")
        {
            $mistakes = $professor->get_mistakes_for_theme($theme);
            if(count($mistakes)) // если в теме есть ошибки
                $th["mistakes_href"] = "/mistakes?theme_id=$theme->id";
            $themes[] = $th;
        }
        else if($theme_status == "open")
            $open_themes[] = $th;

    }

    if($count_users_tasks_in_course == 0)
        $percent = 0;
    else
        $percent = $count_users_tasks_in_course / $count_tasks_in_course;

    $data = [];
    $data["percent"] = round($percent*100);
    $data["tasks"] = $count_users_tasks_in_course;
    $data["all_tasks"] = $count_tasks_in_course;
    $data["themes"] = $themes;
    $data["open_themes"] = $open_themes;

    return $data;

}


// <a id='settings' class='col-12 col-lg-3 btn btn-md mr-5' href='/acc_settings'>Действия с аккаунтом</a>
$content = "<div class='row w-100 p-0 m-0 justify-content-start mb-3'><a id='exit' class='mt-3 mt-lg-0 col-12 col-lg-3 btn btn-md' href='/exit'>Выйти</a></div>";

$user = new User();
$user->id = $_SESSION["id"];
$user->rights = $_SESSION["rights"];
$user->name = $_SESSION["name"];

$professor = new Professor();
$manager = new Manager();
$professor->student = $user;

$users_courses = $manager->get_users_courses($user);
$users_themes = $professor->get_themes();


$content .= "<div class='row w-100 p-0 m-0 h2 justify-content-center mb-3 mt-5'>Статистика</div>";
$content .= "<div class='row w-100 p-0 m-0 justify-content-center'>
            <select class='form-select col-12 col-md-9 col-lg-6 pt-2 pb-2' id='select_form' aria-label='Default select example'>
                <option selected>Выберите курс</option>";
//
foreach ($users_courses as $u_course)
    $content .= "<option value='$u_course->id' class='text-break get_stat_btn'>$u_course->text</option>";

$content .= "</select>
<button id='get_st' class='btn col-12 col-lg-2 m-0 p-0 mt-2 mt-lg-0 ml-lg-2'>Get</button></div>";
$content .= "<div id='statistics' class='container-fluid m-0 p-0'>";

$render = new Render();
foreach ($users_courses as $u_course)
{
    $data = get_stat($u_course, $user);
    $render->argv = ["course_id"=>$u_course->id,"percent"=>$data["percent"], "tasks"=>$data["tasks"], "all_tasks"=>$data["all_tasks"], "themes"=>$data["themes"], "open_themes"=>$data["open_themes"]];
    $render->temp = "course_stat_block.html";
    $content .= $render->render_temp();
}
$content .= "</div>";

$file = basename(__FILE__, ".php");

$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title'=>"acc",
    'css'=>"/css/acc.css",
    "name"=>"<h2>$_SESSION[name]</h2>",
    "content"=>$content,
    "disabled_$file"=>"disabled",
    "js"=>"/js/acc.js",
    "mathjax"=>file_get_contents("templates/mathjax.html")
] ;

echo $page->render_temp();




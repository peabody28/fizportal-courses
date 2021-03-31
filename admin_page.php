<?php
require_once __DIR__."/db.php";
require_once __DIR__."/auth_root.php";
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Course.php";
require_once __DIR__."/classes/Render.php";

if($_GET["code"]=="create_course")
{
    $content = file_get_contents(__DIR__."/templates/create_course_form.html");
}
else if($_GET["code"]=="change_course")
{
    $content = $_GET["id"];
}
else
{

    $content = "<br><br><div class='row col-12 p-0 m-0 ml-5'><a class='btn create' href='/admin_page.php?code=create_course'>Cоздать курс</a> </div><br><br>";
    $courses = new Course();
    $courses_list = $courses->get_courses();
    foreach ($courses_list as $course)
    {
        $block = new Render();
        $block->temp = "course_block_adm.html";
        $block->argv = ["name"=>$course->name, "id"=>$course->id];
        $content .= $block->render_page();
    }
}
$page = new Render();
$page->temp = 'main.html';
$page->argv = ['title' => "main",
    'css' => "/css/admin_page.css",
    "name" => "<h2>$_SESSION[name] - admin-tools</h2>",
    "content" => $content,
    "js" => "/js/admin_page.js",

    'js2'=>"<script src='https://cdn.tiny.cloud/1/3yb9s681223ydg8xq85axmko4fjf3485tan985upzgql2yqm/tinymce/5/tinymce.min.js' referrerpolicy='origin'></script>",
    "textov"=>"<script>
tinymce.init({
    selector: 'textarea',
    plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
    toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
    toolbar_mode: 'floating',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
});
</script>"
    ];
echo $page->render_page();


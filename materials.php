<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Materials_imgs_url_table.php";
require_once __DIR__."/classes/Materials_docs_url_table.php";
require_once __DIR__."/classes/Materials_videos_url_table.php";
session_start();


if (isset($_POST["submit"]))
{

    if ($_POST["code"] == "add_file")
    {

        $uploaddir = __DIR__ . "/media/tasks_materials/$_POST[task_id]/";
        mkdir($uploaddir);
        $apend = "task" . $_POST["task_id"]."_".time().'.pdf';

        $uploadfile = "$uploaddir$apend";

        $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        if ($status)
        {
            $materials_docs_url_table = new Materials_docs_url_table();
            $materials_docs_url_table->create(["task_id"=>$_POST["task_id"], "doc_url"=>"/media/tasks_materials/$_POST[task_id]/".$apend, "file_name"=>$_POST["file_name"]]);
            header("Location: /materials?task_id=$_POST[task_id]");
        }
        else
            header("Location: /materials?task_id=$_POST[task_id]");
    }
    else if ($_POST["code"] == "add_img")
    {
        $uploaddir = __DIR__ . "/media/tasks_materials/$_POST[task_id]/";
        mkdir($uploaddir);
        $inf = pathinfo($_FILES['file']['name']);
        $apend = "task" . $_POST["task_id"]."_".time().".".$inf["extension"];

        $uploadfile = "$uploaddir$apend";

        $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        if ($status)
        {
            $url = "/media/tasks_materials/$_POST[task_id]/".$apend;
            $materials_imgs_url_table = new Materials_imgs_url_table();
            $materials_imgs_url_table->create(["task_id"=>$_POST["task_id"], "img_url"=>"/media/tasks_materials/$_POST[task_id]/".$apend]);
            header("Location: /materials?task_id=$_POST[task_id]");
        }
        else
            header("Location: /materials?task_id=$_POST[task_id]");

    }
    else if ($_POST["code"] == "add_video")
    {

        $materials_videos_url_table = new Materials_videos_url_table();
        $materials_videos_url_table->create(["task_id"=>$_POST["task_id"], "video_url"=>$_POST["video_url"]]);
        header("Location: /materials?task_id=$_POST[task_id]");
    }
    else if ($_POST["code"] == "delete_img")
    {
        $materials_imgs_url_table = new Materials_imgs_url_table();
        $materials_imgs_url_table->delete(["task_id"=>$_POST["task_id"],"img_url"=>$_POST["img_url"]]);
        header("Location: /materials?task_id=$_POST[task_id]");
    }
    else if ($_POST["code"] == "delete_doc")
    {
        $materials_docs_url_table = new Materials_docs_url_table();
        $materials_docs_url_table->delete(["task_id"=>$_POST["task_id"],"file_name"=>$_POST["file_name"]]);
        header("Location: /materials?task_id=$_POST[task_id]");
    }
    else if ($_POST["code"] == "delete_video")
    {
        $materials_videos_url_table = new Materials_videos_url_table();
        $materials_videos_url_table->delete(["task_id"=>$_POST["task_id"], "video_url"=>$_POST["video_url"]]);
        header("Location: /materials?task_id=$_POST[task_id]");
    }
}
else if (isset($_GET["task_id"]))
{
    $task = new Task($_GET["task_id"]);

    $content = "<form action='/theme.php' method='POST'>
                    <input type='hidden' name='submit'>
                    <input type='hidden' name='code' value='back_to_theme'>
                    <input type='hidden' name='id' value='$task->theme_id'>
                    <input type='hidden' name='task_id' value='$task->id'>
                    <button class='btn' id='back_to_theme_btn'>Вернуться к теме</button>
                </form>";

    if ($_SESSION["rights"]=="admin")
    {
        // добавление материалов
        $forms = new Render();
        $forms->temp = "add_materials_to_task_forms.html";
        $forms->argv = ["task_id"=>$task->id];
        $content .= $forms->render_temp();
    }
    // TODO написать метод получения материалов в классе Task
    /*
    $urls = $tasks_materials_table->read($task->id);
    foreach ($urls as $item) {
        foreach ($item as $key=>$url)
        {
            if (!$url)
                continue;
            if ($key=="img_url")
            {
                $content .= "<img src='$url' height='250' alt='img'><br><br>";
            }
            else if ($key=="file_url")
            {
                $name = ($item["file_name"])?:"link";
                $content .= "<a href='$url'>$name</a><br><br>";
            }
            else if ($key=="video_url")
                $content .= "<iframe width='560' height='315' src='$url' frameborder='0' allowfullscreen></iframe>";

        }
    }
    */
    $materials = $task->get_materials();
    //Отображение

    // картинки
    $content .= "<div class='m-0 p-0 row container-fluid mb-4'>";

    if ($materials["imgs"])
    {
        $content .= "<div class='m-0 p-0 col-12 d-flex justify-content-center h2 mb-4'>Картинки</div>";
        foreach ($materials["imgs"] as $url)
            $content .= "<div class='row container m-0 mt-4 p-0 mt-lg-0 col-12 col-lg-6 d-flex justify-content-center'><img src='$url' height='250' alt='img'></div>";
        $content .= "</div>";
    }
    if ($materials["docs"])
    {
        $content .= "<div class='m-0 p-0 d-flex col-12 justify-content-center h2 mb-4'>Полезные ссылки</div>";
        foreach ($materials["docs"] as $doc)
            $content .= "<div class='m-0 p-0 col-12 mt-2 h3'><a href='$doc[doc_url]'>$doc[file_name]</a></div>";
    }
    if ($materials["videos"])
    {
        $content .= "<div class='m-0 p-0 d-flex col-12 justify-content-center h2 mb-4'>Ролики</div>";
        foreach ($materials["videos"] as $item)
            $content .= "<div class='m-0 p-0 col-12 mt-2 h3'><iframe width='560' height='315' src='$item[video_url]' frameborder='0' allowfullscreen></iframe></div>";
    }


    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title'=>"materials for task №$_GET[task_id]",
        'css'=>"/css/materials.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "js"=>"/js/materials.js"
    ];

    echo $page->render_temp();
}
else
    header("Location: /courses");




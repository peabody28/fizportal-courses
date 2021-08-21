<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Materials_imgs_url_table.php";
require_once __DIR__."/classes/Materials_docs_url_table.php";
require_once __DIR__."/classes/Materials_videos_url_table.php";
require_once __DIR__."/classes/Materials_text_table.php";
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
    else if ($_POST["code"] == "add_text")
    {
        $materials_texts_table = new Materials_text_table();
        $materials_texts_table->create(["task_id"=>$_POST["task_id"], "text"=>$_POST["text"]]);
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
    else if ($_POST["code"] == "delete_text")
    {
        $materials_texts_table = new Materials_text_table();
        $materials_texts_table->delete(["task_id"=>$_POST["task_id"], "text"=>$_POST["text"]]);
        header("Location: /materials?task_id=$_POST[task_id]");
    }
}
else if (isset($_GET["task_id"]))
{
    $is_admin = $_SESSION["rights"] == "admin";
    $task = new Task($_GET["task_id"]);

    $content = "<form action='/theme.php' method='POST'>
                    <input type='hidden' name='submit'>
                    <input type='hidden' name='code' value='back_to_theme'>
                    <input type='hidden' name='id' value='$task->theme_id'>
                    <input type='hidden' name='task_id' value='$task->id'>
                    <button class='btn' id='back_to_theme_btn'>Вернуться к теме</button>
                </form>";

    if ($is_admin)
    {
        // добавление материалов
        $forms = new Render();
        $forms->temp = "add_materials_to_task_forms.html";
        $forms->argv = ["task_id"=>$task->id];
        $content .= $forms->render_temp();
    }
    $materials = $task->get_materials();
    //Отображение

    // картинки

    if ($materials["texts"])
    {
        $content .= "<div class='m-0 p-0 col-12 d-flex justify-content-center h2 mb-4'>Подсказки</div><hr>";
        foreach ($materials["texts"] as $text) {
            $content .= "<div class='m-0 p-0 p-3 mb-4 mt-4 col-12 d-flex justify-content-start align-items-center h2 text_block'>$text";
            if ($is_admin)
                $content .= "<form action='materials.php' method='POST' class='col-4 d-flex align-items-center'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='delete_text'>    
                                    <input type='hidden' name='task_id' value='$task->id'>
                                    <input type='hidden' name='text' value='$text'>      
                                    <input type='submit' class='btn del' value='Удалить'>
                                </form>";
           $content .= "</div>";
        }
    }
    if ($materials["imgs"])
    {

        $content .= "<div class='m-0 p-0 col-12 d-flex justify-content-center h2 mb-4'>Картинки</div><hr>";
        $content .= "<div class='m-0 p-0 row container-fluid mb-4'>";
        foreach ($materials["imgs"] as $url)
        {

            if ($is_admin)
            {
                $content .= "<div class='row container m-0 mt-5 p-0 col-12 d-flex justify-content-start img_block'>
                                <img src='$url' width='720' alt='img'>
                                <form action='materials.php' method='POST' class='col-4 d-flex align-items-center'>
                                    <input type='hidden' name='submit'>
                                    <input type='hidden' name='code' value='delete_img'>    
                                    <input type='hidden' name='task_id' value='$task->id'>
                                    <input type='hidden' name='img_url' value='$url'>      
                                    <input type='submit' class='btn del' value='Удалить'>
                                </form>";
            }
            else
                $content .= "<div class='row container m-0 mt-4 p-0 mt-lg-0 col-12 col-lg-6 d-flex justify-content-center'>
                            <img src='$url' width='100%' height='100%' alt='img'>";
            $content .= "</div>";
        }
        $content .= "</div>";


    }
    if ($materials["docs"])
    {
        $content .= "<div class='m-0 p-0 mt-4 d-flex col-12 justify-content-center h2 mb-4'>Полезные ссылки</div><hr>";
        foreach ($materials["docs"] as $doc)
        {
            $content .= "<div class='m-0 p-0 col-12 mt-2 h3 row d-flex justify-content-start'>
                            <a class='col' href='$doc[doc_url]'>$doc[file_name]</a>";
            if($is_admin)
                $content .= "<form  class='col' action='materials.php' method='POST'>
                                <input type='hidden' name='submit'>
                                <input type='hidden' name='code' value='delete_doc'>    
                                <input type='hidden' name='task_id' value='$task->id'>
                                <input type='hidden' name='file_name' value='$doc[file_name]'>      
                                <input type='submit' class='btn del' value='Удалить'>
                            </form>";

            $content .= "</div>";
        }

    }
    if ($materials["videos"])
    {
        $content .= "<div class='m-0 p-0 mt-4 d-flex col-12 justify-content-center h2 mb-4'>Ролики</div><hr>";
        foreach ($materials["videos"] as $item)
        {
            $content .= "<div class='m-0 p-0 col-12 mt-2 h3 '>
                            <iframe class='col-12' width='560' height='315' src='$item' frameborder='0' allowfullscreen></iframe>";
            if($is_admin)
                $content .= "<form class='col-12' action='materials.php' method='POST'>
                                <input type='hidden' name='submit'>
                                <input type='hidden' name='code' value='delete_video'>    
                                <input type='hidden' name='task_id' value='$task->id'>
                                <input type='hidden' name='video_url' value='$item'>      
                                <input type='submit' class='btn del' value='Удалить'>
                            </form>";
            $content .= "</div>";
        }

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




<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Task.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Tasks_materials_table.php";
session_start();


$tasks_materials_table = new Tasks_materials_table();
if (isset($_POST["submit"])) {

    if ($_POST["code"] == "add_file")
    {

        $uploaddir = __DIR__ . "/media/tasks_materials/$_POST[task_id]/";
        mkdir($uploaddir);
        $apend = "task" . $_POST["task_id"]."_".time().'.pdf';

        $uploadfile = "$uploaddir$apend";

        $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        if ($status)
        {
            $tasks_materials_table->add_file(["task_id"=>$_POST["task_id"], "url"=>"/media/tasks_materials/$_POST[task_id]/".$apend, "file_name"=>$_POST["file_name"]]);
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
            $tasks_materials_table->add_img(["task_id"=>$_POST["task_id"], "url"=>"/media/tasks_materials/$_POST[task_id]/".$apend]);
            header("Location: /materials?task_id=$_POST[task_id]");
        }
        else
            header("Location: /materials?task_id=$_POST[task_id]");

    }
    else if ($_POST["code"] == "add_video") {
        $tasks_materials_table->update(["task_id"=>$_POST["task_id"], "url"=>$_POST["video_url"]], "video_url");
        header("Location: /materials?task_id=$_POST[task_id]");
    }
}
else if (isset($_GET["task_id"]))
{
    $content = "";

    $task = new Task($_GET["task_id"]);

    if ($_SESSION["rights"]=="admin")
    {
        // выбор типа материала (от этого зависит папка)

        // добавление материалов
        $forms = new Render();
        $forms->temp = "add_materials_to_task_forms.html";
        $forms->argv = ["task_id"=>$task->id];
        $content .= $forms->render_temp();

        // добавить ссылку

        // встроить видео по ссылке
    }
    $content .= "<form action='/theme.php' method='POST'>
                    <input type='hidden' name='submit'>
                    <input type='hidden' name='code' value='back_to_theme'>
                    <input type='hidden' name='id' value='$task->theme_id'>
                    <input type='hidden' name='task_id' value='$task->id'>
                    <button class='btn' id='back_to_theme_btn'>Вернуться к теме</button>
                </form>";
    // TODO написать метод получения материалов в классе Task
    $urls = $tasks_materials_table->read($task->id);
    foreach ($urls as $item) {
        foreach ($item as $key=>$url)
        {
            if (!$url)
                continue;
            if ($key=="video_url")
                $content .= "<iframe width='560' height='315' src='$url' frameborder='0' allowfullscreen></iframe><br><br>";
            else if ($key=="file_url")
            {
                $name = ($item["file_name"])?:"link";
                $content .= "<a href='$url'>$name</a><br><br>";
            }
            else if ($key=="img_url")
            {
                $content .= "<img src='$url' height='250' alt='img'><br><br>";
            }

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




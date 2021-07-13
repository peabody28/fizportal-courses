<?php
require_once __DIR__."/auth.php";
require_once __DIR__."/classes/Render.php";
require_once __DIR__."/classes/Tasks_table.php";
require_once __DIR__."/classes/Tasks_files_table.php";
session_start();


$tasks_files_table = new Tasks_files_table();
if (isset($_POST["submit"])) {

    if ($_POST["code"] == "add_file")
    {
        $uploaddir = __DIR__ . "/media/tasks_materials/";

        $apend = "task" . $_POST["task_id"] . '.pdf';

        $uploadfile = "$uploaddir$apend";

        $status = move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile);
        if ($status)
        {
            $tasks_files_table->create(["task_id"=>$_POST["task_id"], "url"=>"/media/tasks_materials/".$apend]);
            header("Location: /materials?task_id=$_POST[task_id]");
        }
        else
            header("Location: /materials?task_id=$_POST[task_id]");


    }
}
else if (isset($_GET["task_id"]))
{
    $content = "";
    if ($_SESSION["rights"]=="admin")
    {
        // выбор типа материала (от этого зависит папка)
        $tasks_table = new Tasks_table();
        $tmp_task = $tasks_table->read($_GET["task_id"]);
        // добавление материалов
        $forms = new Render();
        $forms->temp = "add_materials_to_task_forms.html";
        $forms->argv = ["task_id"=>$_GET["task_id"]];
        $content .= $forms->render_temp();

        // добавить ссылку
        // встроить видео по ссылке
        $content .= "<br><br><a class='btn btn-primary' href='/theme?id=$tmp_task[theme_id]&task_id=$_GET[task_id]'>Вернуться к задаче</a><br><br><br>";
    }

    $urls = $tasks_files_table->read($_GET["task_id"]);
    foreach ($urls as $item) {
        $content .= "<a href='$item[url]'>ссылка</a><br><br>";
    }

    $page = new Render();
    $page->temp = 'main.html';
    $page->argv = ['title'=>"materials for task №$_GET[task_id]",
        'css'=>"/css/materials.css",
        "name"=>"<h2>$_SESSION[name]</h2>",
        "content"=>$content,
        "js"=>"/js/materials.js"
    ] ;

    echo $page->render_temp();

}
else
    header("Location: /courses");



<?php
require_once __DIR__."/../vendor/autoload.php";

$loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
$twig = new Twig\Environment($loader);

class Render
{
    public $temp, $argv;
    public function render_course($courses_list): string
    {
        global $twig;
        $courses_blocks = "";
        foreach($courses_list as $cours)
            $courses_blocks .= $twig->render("course-block.html", ["title"=>$cours->title, "text"=>$cours->text, "price"=>$cours->price, "id"=>$cours->id]);
        return  $courses_blocks;
    }
    public function render_temp()
    {
        global $twig;
        return $twig->render($this->temp, $this->argv);
    }
    public function render_theme($themes): string
    {
        global $twig;
        $themes_blocks = "";
        foreach($themes as $theme)
            $themes_blocks .= $twig->render("theme.html", ["title"=>$theme["title"], "text"=>$theme["text"], "id"=>$theme["id"]]);
        return $themes_blocks;
    }
    public function render_task($tasks)
    {
        global $twig;
        $tasks_blocks = "";
        foreach($tasks as $task)
            $tasks_blocks .= $twig->render("task.html", ["text"=>$task["text"], "id"=>$task["id"]]);
        return $tasks_blocks;
    }

}
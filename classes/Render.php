<?php
require_once __DIR__."/../vendor/autoload.php";

$loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
$twig = new Twig\Environment($loader);

class Render
{
    public $temp, $argv;
    public function render_cours($courses_list): string
    {
        global $twig;
        $courses_blocks = "";
        foreach($courses_list as $cours)
            $courses_blocks .= $twig->render("cours-block.html", ["name"=>$cours->name, "title"=>"$cours->title", "id"=>$cours->id]);
        return  $courses_blocks;
    }
    public function render_page()
    {
        global $twig;
        return $twig->render($this->temp, $this->argv);
    }
}
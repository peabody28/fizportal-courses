<?php
require_once __DIR__."/../vendor/autoload.php";


$loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
$twig = new Twig\Environment($loader);


class Render
{
    public $temp, $argv;

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
}
<?php
require_once __DIR__."/Course_block.php";


class Close_course_block extends Course_block
{
    public $temp="close_course_block.html";

    public function render()
    {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
        $twig = new Twig\Environment($loader);
        return $twig->render($this->temp, $this->argv);
    }
}
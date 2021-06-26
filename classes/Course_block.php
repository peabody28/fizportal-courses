<?php


class Course_block
{
    public $temp="course_block.html", $argv;
    public function render()
    {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
        $twig = new Twig\Environment($loader);
        return $twig->render($this->temp, $this->argv);
    }
}
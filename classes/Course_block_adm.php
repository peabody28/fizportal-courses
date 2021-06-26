<?php
require_once __DIR__."/Course_block.php";
require_once __DIR__."/../vendor/autoload.php";


class Course_block_adm extends Course_block
{
    public $temp = "course_block_adm.html";
    public function render()
    {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__."/../templates");
        $twig = new Twig\Environment($loader);
        return $twig->render($this->temp, $this->argv);
    }
}
<?php
require_once __DIR__."/../vendor/autoload.php";
session_start();

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
        foreach ($themes as $theme)
            $themes_blocks .= $twig->render("theme.html", ["title" => $theme["title"], "text" => $theme["text"], "id" => $theme["id"]]);
        return $themes_blocks;
    }

    public function render_themes_adm($themes): string
    {
        global $twig;
        $themes_blocks = "";
        foreach ($themes as $theme)
            $themes_blocks .= $twig->render("theme_block_adm.html", ["title" => $theme["title"], "text" => $theme["text"], "id" => $theme["id"]]);
        return $themes_blocks;
    }

    public function render_task($task)
    {
        $content = "";
        $a_type_task = "<form method='POST' class='send_answer container-fluid'>
                            <input type='hidden' name='submit' >
                            <input type='hidden' name='task_id' value='$task[id]'>
                            <input type='hidden' name='code' value='send_answer'>
                            <div class='row m-0 col-12 d-flex justify-content-center container'>
                                <div class='col-12 col-md-5 row d-flex justify-content-between'>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='answ1' id='inlineCheckbox1' value='1'><label className='form-check-label' htmlFor='inlineCheckbox1'>1</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='answ2' id='inlineCheckbox2' value='2'><label className='form-check-label' htmlFor='inlineCheckbox2'>2</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='answ3' id='inlineCheckbox3' value='3'><label className='form-check-label' htmlFor='inlineCheckbox3'>3</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='answ4' id='inlineCheckbox4' value='4'><label className='form-check-label' htmlFor='inlineCheckbox4'>4</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='answ5' id='inlineCheckbox5' value='5'><label className='form-check-label' htmlFor='inlineCheckbox5'>5</label></div>
                                </div>
                            </div>
                            <div class='row m-0 col-12 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>
                        </form>";

        $b_type_task = "<form class='send_answer' method='POST'>
                    <input type='hidden' name='submit' >
                    <input type='hidden' name='task_id' value='$task[id]'>
                    <input type='hidden' name='code' value='send_answer'>
                    <input type='text' class='row' name='answer'><br>
                    <div class='row d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>
                </form>";

        $content .=
            "<div class='row m-0 p-0 justify-content-center h2'>Условие</div><br>
            <div class='opis m-0 p-0 d-flex justify-content-center'>
                <div class='col-8'>$task[text]</div>
            </div><br><br>
            <div class='container-fluid row m-0 p-0 d-flex justify-content-center'>";

        $content .= ($task["type"]=="A")?$a_type_task:$b_type_task;

        $content.="</div><br><br>";

        if ($_SESSION["rights"]=="admin")
        {
            $content .= "<div class='row justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task[id]'>Изменить задачу</a></div><br><br>";
            $content .= "<form class='del_task' method='POST' onsubmit='del_task();return false;'>
            <input type='hidden' name='submit'>
            <input type='hidden' name='task_id' value='$task[id]'>
            <input type='hidden' name='code' value='del_task'>
            <div class='row d-flex justify-content-center'><button class='btn delete' type='submit'>Удалить эту задачу</button></div>
            </form>";
        }
        return $content;
    }
}
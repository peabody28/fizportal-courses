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

    public function render_theme($theme): string
    {
        global $twig;
        $theme_blocks = $twig->render("theme.html", ["title" => $theme["title"], "text" => $theme["text"], "id" => $theme["id"]]);
        return $theme_blocks;
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
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ1' id='inlineCheckbox1' value='1'><label className='form-check-label' htmlFor='inlineCheckbox1'>1</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ2' id='inlineCheckbox2' value='2'><label className='form-check-label' htmlFor='inlineCheckbox2'>2</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ3' id='inlineCheckbox3' value='3'><label className='form-check-label' htmlFor='inlineCheckbox3'>3</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ4' id='inlineCheckbox4' value='4'><label className='form-check-label' htmlFor='inlineCheckbox4'>4</label></div>
                                    <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ5' id='inlineCheckbox5' value='5'><label className='form-check-label' htmlFor='inlineCheckbox5'>5</label></div>
                                </div>
                            </div>
                            <div class='row m-0 col-12 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>
                        </form>";

        $b_type_task = "<form class='send_answer' method='POST'>
                    <input type='hidden' name='submit' >
                    <input type='hidden' name='task_id' value='$task[id]'>
                    <input type='hidden' name='code' value='send_answer'>
                    <input type='text' class='row' name='$task[id]_b_answer'><br>
                    <div class='row d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>
                </form>";

        $content .=
            "<div class='row m-0 p-0 justify-content-center h2'>Условие</div><br>
            <div class='opis m-0 p-0 d-flex justify-content-center'>
                <div class='col-8'>$task[text]</div>
            </div><br><br>
            <div class='container-fluid row m-0 p-0 d-flex justify-content-center'>";


        $content .= ($task["type"]=="A")?$a_type_task:$b_type_task;

        $content .= "</div><br><br>";

        return $content;
    }

    public function render_supertest_task($task)
    {
        $content = "";
        $a_type_task = "
                        <div class='row m-0 col-12 d-flex justify-content-center container'>
                            <div class='col-12 col-md-5 row d-flex justify-content-between'>
                                <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ1' value='1'><label className='form-check-label'>1</label></div>
                                <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ2' value='2'><label className='form-check-label'>2</label></div>
                                <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ3' value='3'><label className='form-check-label'>3</label></div>
                                <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ4' value='4'><label className='form-check-label'>4</label></div>
                                <div class='row' className='form-check form-check-inline'><input  className='form-check-input' type='checkbox' name='$task[id]_a_answ5' value='5'><label className='form-check-label'>5</label></div>
                            </div>
                        </div>";

        $b_type_task = "<input type='text' class='row' name='$task[id]_b_answer'><br>";

        $content .=
            "<div class='opis m-0 p-0 d-flex justify-content-center'>
                <div class='col-8'>$task[text]</div>
            </div>
            <br>
            <br>
            <div class='container-fluid row m-0 p-0 d-flex justify-content-center'>";

        if($task["type"]=="A") {
            $content .=  $a_type_task;
            $content .= "<input type='hidden' name='$task[id]_a_answ'>";
        }
        else
            $content .= $b_type_task;

        $content.="</div><br><br>";
        return $content;
    }
}
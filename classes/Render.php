<?php
require_once __DIR__."/Theme.php";
require_once __DIR__."/Professor_tasks.php";
require_once __DIR__."/Professor_mistakes.php";
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

    public function render_theme($theme, $class, $progress, $is_admin=false)
    {
        $content = "<div class='row theme $class m-0 p-0 mb-3 ml-2 mr-2 pl-2 pt-1'>
                        <a class='text-start text-break col-12 h2 m-0 p-0' href='/theme?id=$theme->id'>$theme->title</a>
                        <span class='col-12 m-0 p-0'>progress:&nbsp;&nbsp;$progress/$theme->points_limit</span>
                    </div>";
        if($is_admin)
            $content.= "<div class='row m-0 p-0 mb-5 ml-2 mr-2 mt-3'><a class='btn izm' href='/change_theme?id=$theme->id'>Изменить</a></div>";

        return ["block"=>$content];
    }

    public function render_tasks_theme($theme, $tasks_list, $user, $sptest)
    {
        $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>";
        $content .= "<button id='get_text_theme' class='btn mr-1 mt-2' theme_id='$theme->id'></button>";
        // отображение квадратов задачи

        $first_open_id = null;
        $first_solved_id = null;
        for($i=0; $i<count($tasks_list); $i++)
        {
            $task = $tasks_list[$i];
            $last = ($i==count($tasks_list)-1);

            $prof_tasks = new Professor_tasks();
            $task_status = $prof_tasks->task_status($user, $task);

            if($task_status=="solved")
            {
                if($first_solved_id === null)
                    $first_solved_id = $i;
                $button = "<button class='btn' id='$task->id'></button>";
            }
            else if($task_status=="open")
            {
                if($first_open_id === null)
                    $first_open_id = $i;
                $button = "<button class='btn open_btn' id='$task->id'></button>";
            }
            else //status == close
                $button = "<button class='btn close_btn' id='$task->id' disabled></button>";

            $content .= "<form class='get_task mr-1 mt-2' method='POST'>
                            <input type='hidden' name='task_id' value='$task->id'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_task'>
                            $button
                         </form>";
        }

        // задача которая первой отобразится в теме (это должна быть доступная задача (не красная)))
        if ($first_open_id === null)
            $first_id = ($first_solved_id === null)?0:$first_solved_id;
        else
            $first_id = $first_open_id;

        // отображение супертеста
            // лимит задач темы
        $theme->get_points_limit();

            // прогресс
        $prof = new Professor();
        $users_progress = $prof->get_progress_theme($user, $theme);

        $disabled = "";
        if($users_progress < $theme->points_limit && $user->rights!="admin")
            $disabled="disabled";

        $content .= "<form class='get_task mr-1 mt-2 supertest' method='POST'>
                            <input type='hidden' name='supertest_id' value='$sptest->id'>
                            <input type='hidden' name='theme_id' value='$theme->id'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_supertest'>
                            <button class='btn supertest_btn' $disabled></button>
                         </form>";

        $content .= "</div>" ; // закрыл блок с квадратами задач

        // кнопка "добавить задачу"
        if ($user->rights == "admin")
        {
            $content .="<div class='row m-0 mt-3 p-0 pl-3'><a class='btn' id='add_task' href='/add_task?theme_id=$theme->id'>Добавить задачу</a></div>";
            $content .="<div class='row col-12 m-0 mt-3 p-0 pl-3'>
                            <form method='POST' class='m-0 p-0 col-12' id='change_limit_of_points'>
                                <input type='hidden' name='submit'>
                                <input type='hidden' name='code' value='change_limit_of_points'>
                                <input type='hidden' name='id' value='$theme->id'>
                                <div class='row col-12 m-0 p-0 d-flex justify-content-start'>
                                    <button type='submit' class='btn col-12 col-md-3 mr-md-3'>Изменит границу баллов</button>
                                    <div class='m-0 p-0 mr-md-2 col-12 col-md-6 col-lg-4 d-flex align-items-center mt-2 mt-md-0'><input type='text' name='limit_of_points' class='adaptive_input'></div>
                                </div> 
                            </form>
                        </div>";
        }

        return ["content"=>$content, "first_id"=>$first_id];

    }

    public function render_mistakes($mistakes)
    {
        $content = "<div class='row container-fluid justify-content-start m-0 p-0 pl-3'>";

        for($i=0; $i<count($mistakes); $i++)
        {
            $task = $mistakes[$i];

            $content .= "<form class='get_mistake mr-1' method='POST'>
                            <input type='hidden' name='task_id' value='$task->id'>
                            <input type='hidden' name='submit' value='true'>
                            <input type='hidden' name='code' value='get_mistake'>                
                            <button class='btn close_btn' id='$task->id'></button>
                         </form>";
        }

        $content .= "</div><br><br>" ; // закрыл блок с квадратами задач

        return $content;
    }

    public function render_mistake($task)
    {
        // TODO next_id не работает
        return $this->render_task($task, 1);
    }

    public function render_task($task, $is_mistake=false)
    {
        $func = $is_mistake?"send_mistake_answer()":"send_answer()";
        $code = $is_mistake?"send_mistake_answer":"send_answer";

        $content = "";
        $a_type_task = "<form method='POST' class='$code p-0 m-0 row container-fluid' onsubmit='$func;return false;'>
                            <input type='hidden' name='submit' >
                            <input type='hidden' name='task_id' value='$task->id'>
                            <input type='hidden' name='theme_id' value='$task->theme_id'>
                            <input type='hidden' name='code' value='$code'>
                            <div class='col-12 m-0 p-0 d-flex justify-content-center container'>
                                <div class='row m-0 p-0 col-12 col-md-5 row d-flex justify-content-between'>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ1'  value='1' ><br><label class='form-check-label d-flex justify-content-center'>1</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ2'  value='2' ><br><label class='form-check-label d-flex justify-content-center'>2</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ3'  value='3' ><br><label class='form-check-label d-flex justify-content-center'>3</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ4'  value='4' ><br><label class='form-check-label d-flex justify-content-center'>4</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ5'  value='5' ><br><label class='form-check-label d-flex justify-content-center'>5</label></div>
                                </div>
                            </div>
                            <div class='row m-0 mt-3 col-12 d-flex justify-content-center'><button class='btn send' type='submit'>Отправить</button></div>
                        </form>";

        $b_type_task = "<form class='$code container-fluid d-flex justify-content-center m-0 p-0' method='POST' onsubmit='$func;return false;'>
                    <input type='hidden' name='submit' >
                    <input type='hidden' name='task_id' value='$task->id'>
                    <input type='hidden' name='theme_id' value='$task->theme_id'>
                    <input type='hidden' name='code' value='$code'>
                    <div class='row p-0 m-0 col-12 d-flex justify-content-center'>
                        <div class='m-0 p-0 mr-md-2 col-12 col-md-5 col-lg-4 d-flex align-items-center'><input type='text' class='adaptive_input' name='".$task->id."_b_answer'></div>
                        <button class='btn send col-12 col-md-3 text-break mt-2 mt-md-0' type='submit'>Отправить</button>
                    </div>
                    
                </form>";

        $image_block = $task->img_url?"<img src='$task->img_url' alt=''>":"";
        $task->text = str_replace("{{ img }}", "<br><div class='container-fluid row d-flex justify-content-center m-0 p-0 '>".$image_block."</div><br>", $task->text);
        $content .=
            "
            <div class='row opis m-0 p-0 mb-3 d-flex justify-content-center container-fluid'>
                <div class='col-12 m-0 p-0 text-break'>$task->text</div>
            </div>
            <div class='container-fluid row m-0 mt-2 p-0 d-flex justify-content-center'>";


        $content .= ($task->type=="A")?$a_type_task:$b_type_task;

        // ID следующей задачи
        if (!$is_mistake)
        {
            $theme = new Theme($task->theme_id);
            $next_task_id = $theme->get_next_task_id($task);
            if ($next_task_id)
                $content .= "<button type='submit' class='btn next mt-3' onclick='get_next_task($next_task_id);return false;'>Следующая задача</button>";
            else
                $content .= "<button type='submit' id='to_supertest' class='btn mt-3' onclick='$(\".supertest\").submit();return false;'>Перейти к тесту</button>";
        }

        $content .= "</div>";

        return $content;
    }

    public function render_supertest_task($task, $is_admin)
    {
        $content = "";
        $a_type_task = "
                        <div class='col-12 m-0 p-0 d-flex justify-content-center container'>
                                <div class='row m-0 p-0 col-12 col-md-5 row d-flex justify-content-between'>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ1'  value='1' ><label class='form-check-label mt-3 d-flex justify-content-center'>1</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ2'  value='2' ><label class='form-check-label mt-3 d-flex justify-content-center'>2</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ3'  value='3' ><label class='form-check-label mt-3 d-flex justify-content-center'>3</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ4'  value='4' ><label class='form-check-label mt-3 d-flex justify-content-center'>4</label></div>
                                    <div class='col-1 container m-0 p-0 ch_b'><input  class='check-input' type='checkbox' name='".$task->id."_a_answ5'  value='5' ><label class='form-check-label mt-3 d-flex justify-content-center'>5</label></div>
                                </div>
                            </div>";

        $b_type_task = "<input type='text' class='row' name='".$task->id."_b_answer'>";

        $content .=
            "<div class='opis m-0 p-0 mt-5 d-flex justify-content-center'>
                <div class='col-12 m-0 p-0 text-break'>$task->text</div>
            </div> 
            <div class='container-fluid row m-0 p-0 mt-5 d-flex justify-content-center'>";

        if($task->type=="A") {
            $content .=  $a_type_task;
            $content .= "<input type='hidden' name='".$task->id."_a_answ'>"; // TODO зачем это?
        }
        else
            $content .= $b_type_task;

        $content.="</div>";

        if ($is_admin)
        {
            $content .= "<div class='col-12 mt-3 d-flex justify-content-center'><a class='btn chg_task_btn' href='/change_task?id=$task->id&from_supertest=true&theme_id=$task->theme_id'>Изменить задачу</a></div>";
            $content .= " <div class='col-12 mt-3 d-flex justify-content-center'>
                                                <button class='btn del_task' onclick='del_task($task->id);return false;'>Удалить эту задачу</button>
                                           </div>";
        }
        return $content;
    }

    public function render_course($course, $status)
    {
        if($status=="admin")
        {
            $this->temp = "course_block_adm.html";
            $this->argv = ["title" => $course->title, "text" => $course->text, "id" => $course->id, "img_url"=>$course->img_url];
            return $this->render_temp();
        }
        else if($status=="open")
        {
            $this->temp = "course_block.html";
            $this->argv = ["title" => $course->title, "text" => $course->text, "id" => $course->id, "img_url"=>$course->img_url];
            return $this->render_temp();
        }
        else {
            $this->temp = "close_course_block.html";
            $this->argv = ["title" => $course->title, "text" => $course->text, "id" => $course->id, "price"=>$course->price, "img_url"=>$course->img_url];
            return $this->render_temp();
        }
    }
}
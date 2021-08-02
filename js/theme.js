
var first = $('.get_text_theme').next()
first.css('position', 'relative');
first.css('top', '5px');

var intervalId = 0;

$('.get_task').submit(function ()
{
    $('.get_task').css('position', 'static');
    $(this).css('position', 'relative');
    $(this).css('top', '5px');
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                var task = JSON.parse(res)
                $("#tt").html(task["block"])
                $("#message").html("")
                MathJax.typeset() // обновление mathjax
            }
        }
    )
    return false;
})

function get_next_task(id)
{
    var next = $('#'+id).parent()
    next.submit()
}


function send_answer()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.send_answer').serialize(),
            success: function (res)
            {
                var response = JSON.parse(res)
                if (response["status"]=="OK")
                {
                    $("#message").html("Верно!")
                    $("#"+response["task_id"]).css('background-color', '#50C878');
                    if(response["progress"]>=10)
                        $( ".supertest" ).prop( "disabled", false );
                }
                else
                {
                    if(response["code"]=="TIME")
                        $("#content").html("<h2>Время решения темы истекло, возвращайтесь позже</h2>")
                    else if(response["code"]=="IN_MISTAKES")
                        $("#message").html("Вы сможете решить эту задачу в работе над ошибками")
                    else
                    {
                        $("#"+response["task_id"]).css('background-color', '#d53e4f');
                        $("#"+response["task_id"]).prop( "disabled", true );
                        $("#message").html("Неверный ответ!")
                    }

                }

                if($('.lock').length)
                {
                    $('.lock').toggleClass('lock').toggleClass('in_process');
                    inter()
                }

            }
        }
    )
    return false;
}

function send_supertest_answers()
{
    console.log($('.send_supertest_answers').serialize())
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.send_supertest_answers').serialize(),
            success: function (res)
            {
                var response = JSON.parse(res)
                if (response["status"]=="OK")
                {
                    $("#message").html("Верно!")
                    // стопаю таймер
                    if (intervalId)
                        clearInterval(intervalId)
                }
                else
                    $("#message").html("Неверный ответ!")
            }
        }
    )
    return false;
}

function del_task(id)
{
    console.log(id)
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: "submit=true&code=del_task&task_id="+id,
            success: function ()
            {
                location.reload();
            }
        }
    )
    return false;
}

function inter() {

    var Seconds = $('#sec').text(), int;
    var Minutes = $('#min').text(), int;
    var Hours = $('#hours').text(), int;

    intervalId = setInterval(function () { // запускаем интервал
        if (Seconds > 0)
        {
            Seconds--; // вычитаем 1
            if (Math.trunc(Seconds / 10) == 0)
                $('#sec').text("0" + Seconds);
            else
                $('#sec').text(Seconds); // выводим получившееся значение в блок
        }
        else if (Seconds == 0)
        {
            if (Minutes == 0)
            {
                if (Hours == 0)
                {
                    location.reload()
                }
                else
                {
                    Hours--;
                    Seconds = 59;
                    Minutes = 59;
                    $('#sec').text(59)
                    $('#min').text(59)
                    if (Math.trunc(Hours / 10) == 0)
                        $('#hours').text("0" + Hours);
                    else
                        $('#hours').text(Hours);
                }
            } else {
                $('#sec').text(59)
                Seconds = 59;
                Minutes--;
                if (Math.trunc(Minutes / 10) == 0)
                    $('#min').text("0" + Minutes);
                else
                    $('#min').text(Minutes);
            }
        }
    }, 1000);
}

if($('.in_process').length)
    inter()

if($('.blocked').length)
    inter()
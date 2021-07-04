$('.get_task').submit(function ()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                var task = JSON.parse(res)
                $("#task").html(task["block"])
                $("#message").html("")
            }
        }
    )
    return false;
})

$('.send_answer').submit(function send_answer()
{
    console.log($('.send_answer').serialize())
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.send_answer').serialize(),
            success: function (res)
            {
                console.log(res)
                var response = JSON.parse(res)
                if (response["status"]=="OK")
                {
                    $("#message").html("Верно!")
                    $("#"+response["task_id"]).css('background-color', '#50C878');
                }
                else
                    $("#message").html("Неверный ответ!")
            }
        }
    )
    return false;
})

function del_task()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.del_task').serialize(),
            success: function ()
            {
                location.reload();
            }
        }
    )
    return false;
}

$('.get_theme_text').submit(function ()
{
    $.ajax(
        {
            url: "/theme.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                var theme = JSON.parse(res)
                var block = "<div class='row m-0 p-0 justify-content-center h2'>Описание темы</div><br><div class='row m-0 p-0 justify-content-center h2'>"+ theme["text"]+ "</div>"
                $("#task").html(block)
                $("#message").html("")
            }
        }
    )
    return false;
})
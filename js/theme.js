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
                //MathJax.Hub.Queue(["Typeset",MathJax.Hub,"task"]); // обновление mathjax
            }
        }
    )
    return false;
})

$('.send_answer').submit(function ()
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
            url: "/change_task.php",
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
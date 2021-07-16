$('.get_mistake').submit(function ()
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
                MathJax.typeset() // обновление mathjax
            }
        }
    )
    return false;
})

function send_mistake_answer()
{
    console.log()
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
}
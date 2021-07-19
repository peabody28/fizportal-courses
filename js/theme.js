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
                console.log(task["block"])
                $(".tt").html(task["block"])
                $("#message").html("")
                MathJax.typeset() // обновление mathjax
            }
        }
    )
    return false;
})

function send_answer()
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
                    if(response["progress"]>=10)
                        $( ".supertest" ).prop( "disabled", false );
                }
                else
                {
                    if(response["code"]=="TIME")
                        $("#content").html("<h2>Время решения темы истекло, возвращайтесь позже</h2>")
                    else
                        $("#message").html("Неверный ответ!")
                }

            }
        }
    )
    return false;
}

$('#send_supertest_answers').submit(function ()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('#send_supertest_answers').serialize(),
            success: function (res)
            {
                var response = JSON.parse(res)
                if (response["status"]=="OK")
                {
                    $("#message").html("Верно!")
                    $(".supertest").css('background-color', '#50C878');
                }
                else
                    $("#message").html("Неверный ответ!")
            }
        }
    )
    return false;
})

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
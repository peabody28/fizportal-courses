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
                var block = "" +
                    "<div class='row justify-content-center h2'>Условие</div><br>"+
                    "<div class='opis d-flex justify-content-center'>"+task["text"]+"</div><br><br>" +
                    "<div class='container-fluid d-flex justify-content-center'>" +
                        "<form class='send_answer' method='POST' onsubmit='send_answer();return false;'>" +
                            "<input type='hidden' name='submit' >"+
                            "<input type='hidden' name='task_id' value=" + task["id"] + ">"+
                            "<input type='hidden' name='code' value='send_answer'>"+
                            "<input type='text' class='row' name='answer'><br>"+
                            "<div class='row d-flex justify-content-center'><button class='btn' type='submit'>Отправить</button></div>"+
                        "</form>"+
                    "</div>";
                $("#task").html(block)
                $("#message").html("")
            }
        }
    )
    return false;
})

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
                console.log(response)
                if (response["status"]=="OK")
                {
                    $("#message").html("Верно!")
                    $("#"+response["task_id"]).css('background-color', '#25a778');
                }
                else
                    $("#message").html("Неверный ответ!")
            }
        }
    )
    return false;
}
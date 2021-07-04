$('.get_task').submit(function ()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                console.log(res)
                var response = JSON.parse(res)
                var task = response["task"]
                var block = "" +
                    "<div class='row m-0 p-0 justify-content-center h2'>Условие</div><br>"+
                    "<div class='opis m-0 p-0 d-flex justify-content-center'>"+task["text"]+"</div><br><br>" +
                    "<div class='container-fluid d-flex justify-content-center m-0 p-0'>" +
                        "<form class='send_answer' method='POST' onsubmit='send_answer();return false;'>" +
                            "<input type='hidden' name='submit' >"+
                            "<input type='hidden' name='task_id' value=" + task["id"] + ">"+
                            "<input type='hidden' name='code' value='send_answer'>"+
                            "<input type='text' class='row' name='answer'><br>"+
                            "<div class='row d-flex justify-content-center'><button class='btn' type='submit'>Отправить</button></div>"+
                        "</form>"+
                    "</div>" +
                    "<br><br>";
                if (response["create_del_btn"])
                {
                    block += "<form class='del_task' method='POST' onsubmit='del_task();return false;'>" +
                        "<input type='hidden' name='submit'>"+
                        "<input type='hidden' name='task_id' value=" + task["id"] + ">"+
                        "<input type='hidden' name='code' value='del_task'>"+
                        "<div class='row d-flex justify-content-center'><button class='btn delete' type='submit'>Удалить эту задачу</button></div>"+
                        "</form>";
                }

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

function del_task()
{
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.del_task').serialize(),
            success: function (res)
            {

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
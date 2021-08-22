var first = $('.get_mistake:first')
first.css('position', 'relative');
first.css('top', '5px');

$('.get_mistake').submit(function ()
{
    $('.get_mistake').css('position', 'static');
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

function send_mistake_answer()
{
    console.log()
    $.ajax(
        {
            url: "/task.php",
            type: "POST",
            data: $('.send_mistake_answer').serialize(),
            success: function (res)
            {
                var response = JSON.parse(res)
                if (response["status"]=="OK")
                {
                    var element = $("#"+response["task_id"]).parent()
                    var status = element.remove();
                    if(!$('.get_mistake').length)
                        location.reload()
                    else
                        $('.get_mistake:first').submit()
                }
                else
                {
                    $('#message').addClass("red_mess")
                    $('#message').removeClass("green_mess")
                    $("#message").html("Неверный ответ!")
                    $("#"+response["task_id"]).css('background-color', '#d53e4f');
                }
            }
        }
    )
    return false;
}

function get_next_task(id)
{
    var next = $('#'+id).parent()
    next.submit()
}
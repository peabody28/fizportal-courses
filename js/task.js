$("#send_answer").submit(function ()
{

    $.ajax({
            url: "/task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                let response = JSON.parse(res)
                if(response["status"]==="OK")
                    $('#message').html("круто")
                else
                {
                    $("#message").html("Неверный ответ")
                }

            }
        }
    );
    return false;
});
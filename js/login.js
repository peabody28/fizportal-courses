$("#form").submit(function ()
{

    $.ajax({
            url: "/login.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                let response = JSON.parse(res)
                if(response["status"]==="OK")
                    $(location).attr("href", "/courses")
                else
                    $("#error").html(response["error"])
            }
        }
    );
    return false;
});
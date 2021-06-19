$("#exit_form").submit(function ()
{

    $.ajax({
            url: "/exit.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                let response = JSON.parse(res)
                if(response["status"]==="OK")
                    $(location).attr("href", "/index.php")
            }
        }
    );
    return false;
});





$("form").submit(function ()
{
    $.ajax(
        {
            url: "/change_theme.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                {
                    if (res["code"]=="ch_location")
                        $(location).attr("href", "/courses")
                    else
                        $("#message").html(res["message"])
                }
            }
        }
    )
    return false;
});


$("#delete_theme").submit(function ()
{
    $.ajax(
        {
            url: "/change_theme.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res){
                let response = JSON.parse(res)

            }
        }
    )
    return false;
});

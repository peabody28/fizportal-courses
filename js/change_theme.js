

$("form").submit(function ()
{
    CKEDITOR.instances['editor1'].updateElement();
    CKEDITOR.instances['editor2'].updateElement();
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
                if (response["status"]=="OK")
                    $(location).attr("href", "/courses")
            }
        }
    )
    return false;
});

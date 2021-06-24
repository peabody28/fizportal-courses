

$("#change_title_theme").submit(function ()
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
                    $("#message").html("Имя изменено")
                }
            }
        }
    )
    return false;
});

$("#change_text_theme").submit(function ()
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
                    $("#message").html("Описание изменено")
                }
            }
        }
    )
    return false;
});


$("#change_complexity_theme").submit(function ()
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
                    $("#message").html("Сложность изменена")
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
                $(location).attr("href", "/admin_page.php")
            }
        }
    )
    return false;
});

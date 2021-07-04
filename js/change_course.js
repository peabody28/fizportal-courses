

$("#change_title_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
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

$("#change_text_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
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

$("#change_price_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                {
                    $("#message").html("Цена изменена")
                }
            }
        }
    )
    return false;
});

$("#change_complexity_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
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


$("#delete_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res){
                let response = JSON.parse(res)
                $(location).attr("href", "/courses")
            }
        }
    )
    return false;
});

$("#change_img_url_course").submit(function ()
{
    $.ajax(
        {
            url: "/change_course.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                {
                    $("#message").html("Картинка изменена")
                }
            }
        }
    )
    return false;
});

$(".course-actions").submit(function ()
{
    $.ajax({
            url: "/course_actions.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                let response = JSON.parse(res)
                if(response["status"]==="OK")
                   $("#resp").html("good")
                else
                   $("#resp").html(response["error"])
            }
        }
    );
    return false;
});



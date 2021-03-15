$(".cours-doing").submit(function ()
{
    $.ajax({
            url: "/cours-actions.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                let response = JSON.parse(res)
                if(response["status"]==="OK")
                   $("#resp").html("good")
                else
                   $("#resp").html("not work")
            }
        }
    );
    return false;
});



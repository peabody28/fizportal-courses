$("#add_task").submit(function ()
{
    $.ajax(
        {
            url: "/add_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response)
            {
                var resp = JSON.parse(response)
                if (resp["theme_id"])
                {
                    if(resp["supertest_id"])
                        $(location).attr("href", "/theme?id="+resp["theme_id"]+"&supertest")
                    else
                        $(location).attr("href", "/theme?id="+resp["theme_id"])
                }
            }
        }
    )
    return false;
});

$("#A_radio").click(function (){
    $("#A_answers").show();
    $("#B_answers").hide()
});

$("#B_radio").click(function (){
    $("#A_answers").hide();
    $("#B_answers").show()
});

$("input[type = 'radio']").click(function(){
    console.log($(this).attr("checked"))
    if($(this).attr("checked") == 'checked')
    {
        $(this).removeAttr('checked');
    }
    else
    {
        $(this).attr('checked', 'checked')
    }
});
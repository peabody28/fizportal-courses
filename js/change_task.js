$("#A_radio").click(function (){
    $("#a_type").show();
    $("#b_type").hide()
});

$("#B_radio").click(function (){
    $("#a_type").hide();
    $("#b_type").show()
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

$("form").submit(function (){
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                {
                    if (res["code"]=="ch_location")
                        $(location).attr("href", "/theme?id="+res["theme_id"])
                    $("#message").html(res["message"])
                }
            }
        }
    )
    return false;
})
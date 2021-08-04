

$("#A_radio").click(function (){
    $("#a_type").show();
    $("#b_type").hide()
});

$("#B_radio").click(function (){
    $("#a_type").hide();
    $("#b_type").show()
});

$("input[type = 'radio']").click(function(){
    if($(this).attr("checked") == 'checked')
    {
        $(this).removeAttr('checked');
    }
    else
    {
        $(this).attr('checked', 'checked')
    }
});



$("#change_text_task").submit(function (){
    console.log("here")
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                    $("#message").html(res["message"])

            }
        }
    )
    return false;
})

$("#change_answer_task").submit(function (){
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                    $("#message").html(res["message"])

            }
        }
    )
    return false;
})

$("#change_complexity_task").submit(function (){
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                    $("#message").html(res["message"])

            }
        }
    )
    return false;
})

$("#delete_task").submit(function (){
    $.ajax(
        {
            url: "/change_task.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response){
                let res = JSON.parse(response)
                if(res["status"]=="OK")
                    $("#message").html(res["message"])

            }
        }
    )
    return false;
})

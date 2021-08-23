function get_course_stat()
{
    console.log("here")
    var course_id = $(".select_course_form").val();
    console.log(course_id)
    $(".course_stat_block").hide()
    $("#"+course_id).show()
}


$(".get_user").click(
    function ()
    {
        var user_id = $(".select_user_form").val();
        console.log(user_id)
        if (user_id != "null")
        {
            $.ajax(
                {
                    url: "/acc.php",
                    type: "POST",
                    data: "submit=true&code=get_stat_user&user_id="+user_id,
                    success: function (res)
                    {
                        console.log(user_id)
                        var resp = JSON.parse(res)
                        $("#users_stats").html(resp["content"])
                    }
                }
            )
        }
        else
            $("#users_stats").html("")
    }
)
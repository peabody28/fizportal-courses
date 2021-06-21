$("#add_theme").submit(function ()
{
    $.ajax(
        {
            url: "/add_theme.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response)
            {
                var resp = JSON.parse(response)
                console.log("/change_course.php?course_id="+resp["course_id"])
                $(location).attr("href", "/change_course.php?id="+resp["course_id"])
            }
        }
    )
    return false;
});

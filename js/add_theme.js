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
                $(location).attr("href", "/change_course.php?id="+resp["course_id"])
            }
        }
    )
    return false;
});

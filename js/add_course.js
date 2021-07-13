$("#create_course").submit(function ()
{
    $.ajax(
        {
            url: "/add_course.php",
            type: "POST",
            data: $(this).serialize(),
            success: function ()
            {
                $(location).attr("href", "/courses");
            }
        }
    )
    return false;
});
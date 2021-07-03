$("#create_course").submit(function ()
{
    $.ajax(
        {
            url: "/create_course.php",
            type: "POST",
            data: $(this).serialize(),
            success: function ()
            {
                $(location).attr("href", "/admin_page");
            }
        }
    )
    return false;
});
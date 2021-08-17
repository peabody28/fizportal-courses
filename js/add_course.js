$("#create_course").submit(function ()
{
    /*
    CKEDITOR.instances['editor1'].updateElement();
    CKEDITOR.instances['editor2'].updateElement();
    */
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
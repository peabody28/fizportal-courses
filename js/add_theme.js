$("#add_theme").submit(function ()
{
    CKEDITOR.instances['editor1'].updateElement();
    CKEDITOR.instances['editor2'].updateElement();
    $.ajax(
        {
            url: "/add_theme.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (response)
            {
                var resp = JSON.parse(response)
                $(location).attr("href", "/course?id="+resp["course_id"])
            }
        }
    )
    return false;
});

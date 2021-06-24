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
                $(location).attr("href", "/change_theme.php?id="+resp["theme_id"])
            }
        }
    )
    return false;
});

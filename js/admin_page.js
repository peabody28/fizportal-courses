$(".del_course").submit(function ()
{
    $.ajax(
        {
            url: "/course_actions.php",
            type: "POST",
            data: $(this).serialize(),
            success: function ()
            {
                console.log($(this).remove());
            }
        }
    )
   return false;
});



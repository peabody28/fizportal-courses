$('.get_course').submit(
    function ()
    {
        $.ajax({
            url: "/courses.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                var resp = JSON.parse(res)
                if (resp["status"])
                    $(location).attr("href", "/course?id="+resp["course_id"])

            }
        })
        return false;
    }
);
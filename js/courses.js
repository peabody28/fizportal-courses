$('.get_course').submit(
    function ()
    {
        $.ajax({
            url: "/courses.php",
            type: "POST",
            data: $(this).serialize(),
            success: function (res)
            {
                console.log(res)
                var resp = JSON.parse(res)
                if (resp["status"]==="OK")
                    $(location).attr("href", "/course?id="+resp["course_id"])

            }
        })
        return false;
    }
);
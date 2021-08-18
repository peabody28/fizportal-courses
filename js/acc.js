$("#get_st").click(
    function ()
    {
        var course_id = $("#select_form").val();
        $(".course_stat_block").hide()
        $("#"+course_id).show()
    }
)
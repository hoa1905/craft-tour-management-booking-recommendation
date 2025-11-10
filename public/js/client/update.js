$(document).ready(function () {
    $(".tpaccounts .nntitle, .tpaccounts .ddtitle").click(function () {
        if (!$(this).parents(".tpaccounts").hasClass("active")) {
            $(this).parents(".tpaccounts").addClass("active");
        }
        else {
            $(this).parents(".tpaccounts").removeClass("active");
        }
    });
})
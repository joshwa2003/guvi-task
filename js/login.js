$(document).ready(function () {
    if (localStorage.getItem("token")) {
        window.location.replace("profile.html");
    }
    window.history.replaceState(null, null, window.location.href);
    $("#showPass").on("change", function () {
        let type = $(this).is(":checked") ? "text" : "password";
        $("#password").attr("type", type);
    });
});

function login() {
    $("#error-message").text("");

    $.ajax({
        url: "php/login.php",
        type: "POST",
        data: {
            email: $("#email").val(),
            password: $("#password").val()
        },
        success: function (res) {
            localStorage.setItem("token", res.token);
            window.location.replace("profile.html");
        },
        error: function (xhr, status, error) {
            $("#error-message").text("Login failed: " + (xhr.responseJSON ? xhr.responseJSON.message : "Server Error"));
        }
    });
}

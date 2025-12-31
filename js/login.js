function login() {
    $.ajax({
        url: "php/login.php",
        type: "POST",
        data: {
            email: $("#email").val(),
            password: $("#password").val()
        },
        success: function (res) {
            localStorage.setItem("token", res.token);
            window.location.href = "profile.html";
        },
        error: function (xhr, status, error) {
            console.error(xhr);
            alert("Login failed: " + (xhr.responseJSON ? xhr.responseJSON.message : "Server Error"));
        }
    });
}

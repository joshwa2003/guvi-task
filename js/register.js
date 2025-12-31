function register() {
    let email = $("#email").val();
    let password = $("#password").val();
    let confirm = $("#confirm").val();

    if (password !== confirm) {
        alert("Passwords do not match");
        return;
    }

    $.ajax({
        url: "php/register.php",
        type: "POST",
        data: { email, password },
        success: function (res) {
            alert(res.message);
            window.location.href = "login.html";
        },
        error: function (xhr, status, error) {
            console.error(xhr);
            alert("Registration failed: " + (xhr.responseJSON ? xhr.responseJSON.message : "Server Error"));
        }
    });
}

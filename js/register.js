$(document).ready(function () {
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
            $("#email").val("");
            $("#password").val("");
            $("#confirm").val("");
            $("#showPass").prop("checked", false);
        }
    });

    $("#showPass").on("change", function () {
        let type = $(this).is(":checked") ? "text" : "password";
        $("#password").attr("type", type);
        $("#confirm").attr("type", type);
    });
});

function register() {
    let email = $("#email").val();
    let password = $("#password").val();
    let confirm = $("#confirm").val();

    $("#error-message").text("");

    let emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        $("#error-message").text("Invalid email format");
        return;
    }

    let missing = [];

    if (password.length < 8) missing.push("8 chars");
    if (!/[A-Z]/.test(password)) missing.push("1 uppercase");
    if (!/[a-z]/.test(password)) missing.push("1 lowercase");
    if (!/[0-9]/.test(password)) missing.push("1 number");
    if (!/[@$!%*?&]/.test(password)) missing.push("1 special char");

    if (missing.length > 0) {
        $("#error-message").text("Password missing " + missing.join(", "));
        return;
    }

    if (password !== confirm) {
        $("#error-message").text("Passwords do not match");
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

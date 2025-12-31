$(document).ready(function () {
    $.ajax({
        url: "php/profile.php",
        headers: {
            Authorization: localStorage.getItem("token")
        },
        success: function (res) {
            $("#name").val(res.name);
            $("#age").val(res.age);
            $("#dob").val(res.dob);
            $("#contact").val(res.contact);
        }
    });
});

function updateProfile() {
    $.ajax({
        url: "php/profile.php",
        type: "POST",
        headers: {
            Authorization: localStorage.getItem("token")
        },
        data: {
            name: $("#name").val(),
            age: $("#age").val(),
            dob: $("#dob").val(),
            contact: $("#contact").val()
        },
        success: function () {
            alert("Profile updated");
        }
    });
}

function logout() {
    localStorage.removeItem("token");
    window.location.href = "login.html";
}

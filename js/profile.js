$(document).ready(function () {
    if (!localStorage.getItem("token")) {
        window.location.replace("login.html");
        return;
    }

    window.history.pushState(null, null, window.location.href);

    window.addEventListener('popstate', function (event) {
        window.history.pushState(null, null, window.location.href);
    });

    setDateLimits();
    loadUserProfile();

    $("#dob").on("change", function () {
        let selectedDate = $(this).val();
        if (selectedDate) {
            let calculatedAge = getAge(selectedDate);
            $("#age").val(calculatedAge);
        }
    });
});

function setDateLimits() {
    let today = new Date();

    let tenYearsAgo = new Date();
    tenYearsAgo.setFullYear(today.getFullYear() - 10);

    let oneHundredTwentyYearsAgo = new Date();
    oneHundredTwentyYearsAgo.setFullYear(today.getFullYear() - 120);

    let maxDate = tenYearsAgo.toISOString().split('T')[0];
    let minDate = oneHundredTwentyYearsAgo.toISOString().split('T')[0];

    $("#dob").attr("max", maxDate);
    $("#dob").attr("min", minDate);
}

function getAge(birthDate) {
    let today = new Date();
    let birth = new Date(birthDate);

    let age = today.getFullYear() - birth.getFullYear();

    let birthdayNotYetThisYear = false;

    if (today.getMonth() < birth.getMonth()) {
        birthdayNotYetThisYear = true;
    }

    if (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate()) {
        birthdayNotYetThisYear = true;
    }

    if (birthdayNotYetThisYear) {
        age = age - 1;
    }

    return age;
}

function loadUserProfile() {
    $.ajax({
        url: "php/profile.php",
        headers: {
            Authorization: localStorage.getItem("token")
        },
        success: function (data) {
            $("#name").val(data.name);
            $("#dob").val(data.dob);
            $("#age").val(data.age);
            $("#contact").val(data.contact);
        }
    });
}

function updateProfile() {
    let name = $("#name").val().trim();
    let dob = $("#dob").val();
    let age = $("#age").val();
    let contact = $("#contact").val().trim();

    $("#error-message").text("");

    name = name.replace(/\s+/g, " ");

    if (!name) {
        $("#error-message").text("Name is required");
        return;
    }

    if (name.length < 3) {
        $("#error-message").text("Name must be at least 3 characters");
        return;
    }

    if (name.length > 25) {
        $("#error-message").text("Name must be less than 25 characters");
        return;
    }

    let namePattern = /^[A-Za-z]+(\.?[A-Za-z]+)*(\s[A-Za-z]+(\.?[A-Za-z]+)*)*$/;
    if (!namePattern.test(name)) {
        $("#error-message").text("Name can only contain letters, spaces, and single dots");
        return;
    }

    if (!dob) {
        $("#error-message").text("Date of birth is required");
        return;
    }

    let birthDate = new Date(dob);
    let today = new Date();

    let tenYearsAgo = new Date();
    tenYearsAgo.setFullYear(today.getFullYear() - 10);

    if (birthDate > tenYearsAgo) {
        $("#error-message").text("You must be at least 10 years old");
        return;
    }

    let oneHundredTwentyYearsAgo = new Date();
    oneHundredTwentyYearsAgo.setFullYear(today.getFullYear() - 120);

    if (birthDate < oneHundredTwentyYearsAgo) {
        $("#error-message").text("Age cannot be more than 120 years");
        return;
    }

    if (!contact) {
        $("#error-message").text("Contact number is required");
        return;
    }

    if (contact.length !== 10) {
        $("#error-message").text("Contact must be exactly 10 digits");
        return;
    }

    let firstDigit = contact[0];
    if (firstDigit !== "6" && firstDigit !== "7" && firstDigit !== "8" && firstDigit !== "9") {
        $("#error-message").text("Contact must start with 6, 7, 8, or 9");
        return;
    }

    let contactPattern = /^[0-9]+$/;
    if (!contactPattern.test(contact)) {
        $("#error-message").text("Contact must contain only numbers");
        return;
    }

    let allSameDigit = true;
    for (let i = 1; i < contact.length; i++) {
        if (contact[i] !== contact[0]) {
            allSameDigit = false;
            break;
        }
    }

    if (allSameDigit) {
        $("#error-message").text("Contact number appears invalid");
        return;
    }

    $.ajax({
        url: "php/profile.php",
        type: "POST",
        headers: {
            Authorization: localStorage.getItem("token")
        },
        data: {
            name: name,
            age: age,
            dob: dob,
            contact: contact
        },
        success: function () {
            alert("Profile updated successfully");
        }
    });
}

function logout() {
    localStorage.removeItem("token");
    window.location.replace("login.html");
}

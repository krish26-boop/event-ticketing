$(document).ready(function () {
    // Base API URL
    const API_URL = "http://localhost:8000/api";

    // Register
    $("#register-form").submit(function (e) {
        e.preventDefault();

        let name = $("#reg-name").val();
        let email = $("#reg-email").val();
        let password = $("#reg-password").val();
        let confirmPassword = $("#reg-confirm-password").val();
        let role = $("#reg-role").val();

        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        $.ajax({
            url: `${API_URL}/register`,
            type: "POST",
            data: {
                name: name,
                email: email,
                password: password,
                password_confirmation: confirmPassword,
                role: role
            },
            success: function (response) {
                alert("Registration successful! Please login.");
            },
            error: function (xhr) {
                alert("Registration failed: " + xhr.responseJSON.message);
            }
        });
    });

    // Login
    $("#login-form").submit(function (e) {
        e.preventDefault();

        let email = $("#login-email").val();
        let password = $("#login-password").val();

        $.ajax({
            url: `${API_URL}/login`,
            type: "POST",
            data: {
                email: email,
                password: password
            },
            success: function (response) {
                localStorage.setItem("token", response.token);
                localStorage.setItem("user", JSON.stringify(response.user));
                window.location.href = "dashboard.html";
            },
            error: function (xhr) {
                alert("Login failed: " + xhr.responseJSON.message);
            }
        });
    });

    // Check authentication on dashboard
    if (window.location.pathname.includes("dashboard.html")) {
        let token = localStorage.getItem("token");
        let user = JSON.parse(localStorage.getItem("user"));

        if (!token || !user) {
            alert("Unauthorized! Redirecting to login.");
            window.location.href = "index.html";
        } else {
            $("#user-info").text(`Logged in as: ${user.name} (${user.email})`);
        }
    }

    // Logout
    $("#logout-btn").click(function () {
        let token = localStorage.getItem("token");

        $.ajax({
            url: `${API_URL}/logout`,
            type: "POST",
            headers: { Authorization: "Bearer " + token },
            success: function () {
                localStorage.removeItem("token");
                localStorage.removeItem("user");
                window.location.href = "index.html";
            },
            error: function (xhr) {
                alert("Logout failed: " + xhr.responseJSON.message);
            }
        });
    });
});

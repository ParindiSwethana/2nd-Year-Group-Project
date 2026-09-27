document.addEventListener("DOMContentLoaded", function () {
    // Username is always "<District> Office"; the server sets the same value when saving
    const district = document.getElementById("district");
    const username = document.getElementById("username");

    if (district && username) {
        district.addEventListener("change", function () {
            username.value = district.value !== "" ? district.value + " Office" : "";
        });
    }

    // Show / hide password (same behaviour as the registration page)
    document.querySelectorAll(".toggle-password").forEach(function (button) {
        button.addEventListener("click", function () {
            const passwordInput = document.getElementById(button.dataset.target);

            if (!passwordInput) {
                return;
            }

            const isHidden = passwordInput.type === "password";
            passwordInput.type = isHidden ? "text" : "password";
            button.classList.toggle("fa-eye", !isHidden);
            button.classList.toggle("fa-eye-slash", isHidden);
        });
    });
});

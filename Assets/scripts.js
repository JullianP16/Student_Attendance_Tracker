document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("darkModeToggle");

    // Load stored preference
    if (localStorage.getItem("darkMode") === "enabled") {
        document.body.classList.add("dark-mode");
        if (toggle) toggle.checked = true;
    }

    // Toggle dark mode on switch
    if (toggle) {
        toggle.addEventListener("change", function () {
            if (this.checked) {
                document.body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                document.body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });
    }
});

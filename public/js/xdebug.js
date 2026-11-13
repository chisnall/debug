document.addEventListener('DOMContentLoaded', () => {
    // Apply theme
    function applyTheme(theme) {
        // Check for system theme
        if (theme === "system") {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (prefersDark) theme = 'dark';
            else theme = 'light';
        }

        if (theme === "dark") document.documentElement.setAttribute("data-theme", "dark");
        else if (theme === "light") document.documentElement.setAttribute("data-theme", "light");
    }

    let currentTheme = localStorage.getItem("currentTheme") ?? 'system';

    // Apply theme
    applyTheme(currentTheme);
});

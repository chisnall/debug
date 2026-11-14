document.addEventListener('DOMContentLoaded', () => {
    function applyTheme(theme) {
        if (theme === "system") {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (prefersDark) theme = 'dark';
            else theme = 'light';
        }

        if (theme === "dark") document.documentElement.setAttribute("data-theme", "dark");
        else if (theme === "light") document.documentElement.setAttribute("data-theme", "light");
    }

    function styleThemeButtons(theme) {
        themeButtons.forEach((button, index) => {
            if (button) button.classList.toggle("selected", themes[index] === theme);
        });
    }

    const themes = ['system', 'light', 'dark'];
    const themeButtons = themes.map(theme => document.getElementById(`icon-${theme}`));

    const currentTheme = localStorage.getItem("currentTheme") ?? 'system';

    themeButtons.forEach((button, index) => {
        if (button) {
            button.addEventListener("click", () => {
                const theme = themes[index];
                localStorage.setItem("currentTheme", theme);
                styleThemeButtons(theme);
                applyTheme(theme);
            });
        }
    });

    styleThemeButtons(currentTheme);
    applyTheme(currentTheme);
});

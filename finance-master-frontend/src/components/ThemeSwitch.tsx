import React, { useEffect, useState } from "react";

const ThemeSwitch = () => {
    const [isDark, setIsDark] = useState(false);

    useEffect(() => {
        const saved = localStorage.getItem("theme");
        if (saved === "dark") {
            document.documentElement.classList.add("dark");
            setIsDark(true);
        }
    }, []);

    const toggleTheme = () => {
        if (document.documentElement.classList.contains("dark")) {
            document.documentElement.classList.remove("dark");
            localStorage.setItem("theme", "light");
            setIsDark(false);
        } else {
            document.documentElement.classList.add("dark");
            localStorage.setItem("theme", "dark");
            setIsDark(true);
        }
    };

    return (
        <button
            onClick={toggleTheme}
            className="ml-3 px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-white border"
            aria-label="Tema değiştir"
        >
            {isDark ? "🌙 Koyu" : "☀️ Açık"}
        </button>
    );
};
export default ThemeSwitch;

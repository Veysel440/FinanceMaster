import React, { useEffect, useState } from "react";
export default function ThemeSwitch() {
    const [mode, setMode] = useState<"light"|"dark"|"system">("system");
    useEffect(() => {
        const saved = (localStorage.getItem("theme") as typeof mode) || "system";
        setMode(saved);
    }, []);
    const apply = (v: typeof mode) => {
        const root = document.documentElement;
        if (v === "system") {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            root.classList.toggle("dark", prefersDark);
        } else {
            root.classList.toggle("dark", v === "dark");
        }
        localStorage.setItem("theme", v);
        setMode(v);
    };
    return (
        <div className="inline-flex items-center gap-2 text-sm">
            <span>Tema:</span>
            <select className="border rounded px-2 py-1 bg-white dark:bg-gray-800" value={mode} onChange={e => apply(e.target.value as any)}>
                <option value="system">Sistem</option>
                <option value="light">Açık</option>
                <option value="dark">Koyu</option>
            </select>
        </div>
    );
}

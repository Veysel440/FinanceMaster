import React, { useEffect } from "react";
import ProtectedRoute from "../components/ProtectedRoute";
import { useForm } from "react-hook-form";
import toast from "react-hot-toast";
import api from "../api/axios";
import { useAuth } from "../context/AuthContext";

type SettingsForm = {
    currency: "TRY" | "USD" | "EUR";
    locale: "tr" | "en";
    email_reports: boolean;
    theme: "light" | "dark" | "system";
};

export default function Settings() {
    const { user, setUser } = useAuth() as any;
    const { register, handleSubmit, reset, formState: { isSubmitting } } = useForm<SettingsForm>({
        defaultValues: { currency: "TRY", locale: "tr", email_reports: true, theme: "system" }
    });

    useEffect(() => {
        if (user) {
            reset({
                currency: user.currency ?? "TRY",
                locale: user.locale ?? "tr",
                email_reports: !!user.email_reports,
                theme: (localStorage.getItem("theme") as SettingsForm["theme"]) ?? "system",
            });
        }
    }, [user, reset]);

    const applyTheme = (theme: SettingsForm["theme"]) => {
        const root = document.documentElement;
        if (theme === "system") {
            const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            root.classList.toggle("dark", prefersDark);
            localStorage.setItem("theme", "system");
        } else {
            root.classList.toggle("dark", theme === "dark");
            localStorage.setItem("theme", theme);
        }
    };

    const onSubmit = async (data: SettingsForm) => {
        try {
            await api.post("/settings", { currency: data.currency, locale: data.locale, email_reports: data.email_reports });
            setUser && setUser({ ...user, currency: data.currency, locale: data.locale, email_reports: data.email_reports });
            applyTheme(data.theme);
            toast.success("Ayarlar güncellendi");
        } catch {
            toast.error("Ayarlar kaydedilemedi");
        }
    };

    return (
        <ProtectedRoute>
            <main className="max-w-lg mx-auto p-4 space-y-4">
                <h2 className="text-xl font-bold">Ayarlar</h2>
                <form onSubmit={handleSubmit(onSubmit)} className="space-y-3">
                    <div>
                        <label className="block text-sm mb-1">Para Birimi</label>
                        <select {...register("currency")} className="border rounded p-2 w-full">
                            <option value="TRY">TRY</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm mb-1">Dil</label>
                        <select {...register("locale")} className="border rounded p-2 w-full">
                            <option value="tr">Türkçe</option>
                            <option value="en">English</option>
                        </select>
                    </div>

                    <label className="flex items-center gap-2">
                        <input type="checkbox" {...register("email_reports")} />
                        Aylık e-posta özeti al
                    </label>

                    <div>
                        <label className="block text-sm mb-1">Tema</label>
                        <select {...register("theme")} className="border rounded p-2 w-full">
                            <option value="system">Sistem</option>
                            <option value="light">Açık</option>
                            <option value="dark">Koyu</option>
                        </select>
                    </div>

                    <button disabled={isSubmitting} className="px-4 py-2 rounded bg-blue-600 text-white">
                        {isSubmitting ? "Kaydediliyor…" : "Kaydet"}
                    </button>
                </form>
            </main>
        </ProtectedRoute>
    );
}

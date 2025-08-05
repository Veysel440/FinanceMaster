import React, { useState } from "react";
import api from "../api/axios";
import toast from "react-hot-toast";
import ProtectedRoute from "../components/ProtectedRoute";

const Settings = () => {
    const [form, setForm] = useState({ current_password: "", new_password: "", new_password_confirmation: "" });
    const [loading, setLoading] = useState(false);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm(f => ({ ...f, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (form.new_password !== form.new_password_confirmation) {
            toast.error("Yeni şifreler eşleşmiyor!"); return;
        }
        setLoading(true);
        try {
            await api.put("/password", {
                current_password: form.current_password,
                password: form.new_password,
                password_confirmation: form.new_password_confirmation,
            });
            toast.success("Şifre güncellendi!");
            setForm({ current_password: "", new_password: "", new_password_confirmation: "" });
        } catch (err: any) {
            toast.error(err?.response?.data?.message || "Şifre güncellenemedi!");
        }
        setLoading(false);
    };

    return (
        <ProtectedRoute>
            <div className="max-w-lg mx-auto p-4">
                <h2 className="text-lg font-bold mb-3">Şifre Değiştir</h2>
                <form onSubmit={handleSubmit} className="flex flex-col gap-3">
                    <input
                        name="current_password"
                        value={form.current_password}
                        onChange={handleChange}
                        type="password"
                        placeholder="Mevcut şifre"
                        className="border rounded p-2"
                        required
                    />
                    <input
                        name="new_password"
                        value={form.new_password}
                        onChange={handleChange}
                        type="password"
                        placeholder="Yeni şifre"
                        className="border rounded p-2"
                        minLength={8}
                        required
                    />
                    <input
                        name="new_password_confirmation"
                        value={form.new_password_confirmation}
                        onChange={handleChange}
                        type="password"
                        placeholder="Yeni şifre (tekrar)"
                        className="border rounded p-2"
                        minLength={8}
                        required
                    />
                    <button type="submit" disabled={loading} className="bg-blue-600 text-white px-4 py-2 rounded">
                        {loading ? "Güncelleniyor..." : "Şifreyi Güncelle"}
                    </button>
                </form>
            </div>
        </ProtectedRoute>
    );
};
export default Settings;

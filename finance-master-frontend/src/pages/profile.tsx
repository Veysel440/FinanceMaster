import React, { useEffect, useRef, useState } from "react";
import { useForm } from "react-hook-form";
import * as yup from "yup";
import { yupResolver } from "@hookform/resolvers/yup";
import toast from "react-hot-toast";
import { useAuth } from "../context/AuthContext";
import api from "../api/axios";
import ProfileAvatar from "../components/ProfileAvatar";
import ProtectedRoute from "../components/ProtectedRoute";

type ProfileForm = {
    name: string;
    email: string;
    currency?: string;
};

const schema = yup.object({
    name: yup.string().required("İsim zorunlu!"),
    email: yup.string().email("Geçersiz e-posta!").required("E-posta zorunlu!"),
    currency: yup.string().oneOf(["TRY", "USD", "EUR"]).optional(),
});

const Profile = () => {
    const { user, setUser } = useAuth() as any;
    const { register, handleSubmit, reset, formState: { errors, isSubmitting } } = useForm<ProfileForm>({
        resolver: yupResolver(schema),
        defaultValues: {
            name: user?.name || "",
            email: user?.email || "",
            currency: user?.currency || "",
        }
    });

    useEffect(() => {
        if (user) {
            reset({
                name: user.name || "",
                email: user.email || "",
                currency: user.currency || "",
            });
        }
    }, [user, reset]);

    const [file, setFile] = useState<File | null>(null);
    const inputRef = useRef<HTMLInputElement | null>(null);

    const uploadProfilePhoto = async () => {
        if (!file) return;
        const formData = new FormData();
        formData.append("profile_photo", file);
        try {
            const res = await api.post("/profile/photo", formData, { headers: { "Content-Type": "multipart/form-data" } });
            setUser(res.data);
            toast.success("Profil fotoğrafı güncellendi!");
            setFile(null);
            if (inputRef.current) inputRef.current.value = "";
        } catch {
            toast.error("Profil fotoğrafı yüklenemedi!");
        }
    };

    const onSubmit = async (data: ProfileForm) => {
        try {
            const res = await api.put("/profile", data);
            setUser(res.data);
            toast.success("Profil güncellendi!");
        } catch {
            toast.error("Profil güncellenemedi!");
        }
    };

    return (
        <ProtectedRoute>
            <div className="max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
                <h2 className="text-2xl font-bold mb-4">Profil</h2>
                <div className="flex items-center gap-4 mb-6">
                    <ProfileAvatar user={user} size={64} />
                    <div>
                        <input
                            ref={inputRef}
                            type="file"
                            accept="image/*"
                            onChange={e => setFile(e.target.files?.[0] ?? null)}
                            className="mb-2"
                        />
                        <button
                            onClick={uploadProfilePhoto}
                            disabled={!file}
                            className="bg-blue-500 text-white px-3 py-1 rounded disabled:bg-gray-400"
                        >
                            Fotoğrafı Yükle
                        </button>
                    </div>
                </div>
                <form
                    onSubmit={handleSubmit(onSubmit)}
                    className="flex flex-col gap-3"
                >
                    <input {...register("name")} placeholder="Ad Soyad" className="p-2 border rounded" />
                    {errors.name && <span className="text-red-500">{errors.name.message}</span>}
                    <input {...register("email")} placeholder="E-posta" className="p-2 border rounded" />
                    {errors.email && <span className="text-red-500">{errors.email.message}</span>}
                    <select {...register("currency")} className="p-2 border rounded">
                        <option value="">Para birimi seçin</option>
                        <option value="TRY">TL</option>
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                    </select>
                    <button
                        disabled={isSubmitting}
                        type="submit"
                        className="bg-blue-600 text-white p-2 rounded mt-2"
                    >
                        {isSubmitting ? "Güncelleniyor..." : "Güncelle"}
                    </button>
                </form>
            </div>
        </ProtectedRoute>
    );
};

export default Profile;

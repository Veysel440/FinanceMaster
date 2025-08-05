import React, { useState, useEffect } from "react";
import { useRouter } from "next/router";
import { useAuth } from "../context/AuthContext";
import toast from "react-hot-toast";

const Login = () => {
    const { login, user } = useAuth();
    const router = useRouter();
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        if (user) router.replace("/");
    }, [user, router]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        const success = await login(email, password);
        setLoading(false);
        if (success) {
            toast.success("Giriş başarılı!");
            router.push("/");
        }
        else toast.error("E-posta veya şifre hatalı.");
    };

    return (
        <form onSubmit={handleSubmit} className="max-w-md mx-auto mt-24 p-8 border rounded bg-white shadow-md">
            <h2 className="text-xl font-bold mb-4">Giriş Yap</h2>
            <input type="email" placeholder="E-posta"
                   value={email}
                   onChange={e => setEmail(e.target.value)}
                   required
                   className="w-full mb-3 p-2 border rounded"
            />
            <input type="password" placeholder="Şifre"
                   value={password}
                   onChange={e => setPassword(e.target.value)}
                   required
                   className="w-full mb-4 p-2 border rounded"
            />
            <button type="submit"
                    disabled={loading}
                    className="w-full p-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                {loading ? "Giriş yapılıyor..." : "Giriş Yap"}
            </button>
        </form>
    );
};
export default Login;

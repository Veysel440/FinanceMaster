import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";
import toast from "react-hot-toast";

type Notification = {
    id: number;
    title: string;
    body: string;
    read: boolean;
    created_at: string;
};

export default function Notifications() {
    const [items, setItems] = useState<Notification[]>([]);
    const [loading, setLoading] = useState(true);

    const fetchItems = async () => {
        try {
            const r = await api.get("/notifications");
            setItems(r.data.data ?? r.data);
        } catch {
            toast.error("Bildirimler alınamadı");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { fetchItems(); }, []);

    const markRead = async (id: number) => {
        try {
            await api.post(`/notifications/${id}/read`);
            setItems(prev => prev.map(n => n.id === id ? { ...n, read: true } : n));
        } catch {
            toast.error("Güncellenemedi");
        }
    };

    const markAllRead = async () => {
        try {
            await api.post(`/notifications/read-all`);
            setItems(prev => prev.map(n => ({ ...n, read: true })));
            toast.success("Tümü okundu");
        } catch {
            toast.error("İşlem başarısız");
        }
    };

    if (loading) return <div>Yükleniyor…</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-2xl mx-auto p-4">
                <div className="flex items-center justify-between mb-3">
                    <h2 className="text-xl font-bold">Bildirimler</h2>
                    <button onClick={markAllRead} className="text-sm px-3 py-1 rounded bg-blue-600 text-white">
                        Tümünü Okundu İşaretle
                    </button>
                </div>
                <ul className="space-y-3">
                    {items.length === 0 && <li>Bildirim yok.</li>}
                    {items.map(n => (
                        <li key={n.id} className={`p-3 rounded border ${n.read ? "bg-gray-50 dark:bg-gray-800" : "bg-blue-50 dark:bg-blue-900/30"}`}>
                            <div className="flex items-center justify-between">
                                <div className="font-semibold">{n.title}</div>
                                <div className="text-xs opacity-70">{new Date(n.created_at).toLocaleString()}</div>
                            </div>
                            <div className="text-sm mt-1">{n.body}</div>
                            {!n.read && (
                                <button onClick={() => markRead(n.id)} className="mt-2 text-xs px-2 py-1 rounded bg-blue-600 text-white">
                                    Okundu
                                </button>
                            )}
                        </li>
                    ))}
                </ul>
            </main>
        </ProtectedRoute>
    );
}

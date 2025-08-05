import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";

type Notification = {
    id: number;
    title: string;
    body: string;
    read: boolean;
    created_at: string;
};

const Notifications = () => {
    const [notifications, setNotifications] = useState<Notification[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        api.get("/notifications")
            .then(res => setNotifications(res.data.data || res.data))
            .finally(() => setLoading(false));
    }, []);

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-lg mx-auto px-4 py-8">
                <h2 className="text-xl font-bold mb-3">Bildirimler</h2>
                <ul>
                    {notifications.map(n => (
                        <li key={n.id} className={`mb-3 p-3 rounded ${n.read ? "bg-gray-100" : "bg-blue-50"}`}>
                            <div className="font-bold">{n.title}</div>
                            <div className="text-sm text-gray-500">{n.created_at}</div>
                            <div>{n.body}</div>
                        </li>
                    ))}
                    {notifications.length === 0 && <li>Bildiriminiz yok.</li>}
                </ul>
            </main>
        </ProtectedRoute>
    );
};
export default Notifications;

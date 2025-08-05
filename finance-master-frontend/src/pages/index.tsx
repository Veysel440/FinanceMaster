import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";

type TrendItem = { month: string; income: number; expense: number };

const Dashboard = () => {
    const [summary, setSummary] = useState({ income: 0, expense: 0, balance: 0 });
    const [trend, setTrend] = useState<TrendItem[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            api.get("/reports?period=monthly"),
            api.get("/reports?period=monthly&trend=1")
        ])
            .then(([s, t]) => {
                setSummary(s.data.summary || s.data.data?.summary || {});
                const labels = t.data.trendData?.labels || [];
                setTrend(
                    labels.map((month: string, i: number) => ({
                        month,
                        income: t.data.trendData.income[i],
                        expense: t.data.trendData.expense[i],
                    }))
                );
            })
            .finally(() => setLoading(false));
    }, []);

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-2xl w-full mx-auto px-4 py-8">
                <h2 className="text-2xl font-bold mb-3">Finansal Özet</h2>
                <div className="flex flex-wrap gap-4 mb-6">
                    <div className="flex-1 bg-green-100 p-4 rounded">Gelir: <b>{summary.income}₺</b></div>
                    <div className="flex-1 bg-red-100 p-4 rounded">Gider: <b>{summary.expense}₺</b></div>
                    <div className="flex-1 bg-blue-100 p-4 rounded">Bakiye: <b>{summary.balance}₺</b></div>
                </div>
                <h3 className="font-bold mb-2">Aylık Gelir-Gider Grafiği</h3>
                <ResponsiveContainer width="100%" height={320}>
                    <LineChart data={trend}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="month" />
                        <YAxis />
                        <Tooltip />
                        <Line type="monotone" dataKey="income" stroke="#22c55e" name="Gelir" />
                        <Line type="monotone" dataKey="expense" stroke="#ef4444" name="Gider" />
                    </LineChart>
                </ResponsiveContainer>
            </main>
        </ProtectedRoute>
    );
};
export default Dashboard;

import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";
import DashboardCards from "../components/DashboardCards";
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";

export default function Dashboard() {
    const [summary, setSummary] = useState({ income: 0, expense: 0, balance: 0 });
    const [trend, setTrend] = useState<{ month: string; income: number; expense: number }[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        (async () => {
            const s = await api.get("/reports?period=monthly");
            const t = await api.get("/reports?period=monthly&trend=1");
            setSummary(s.data.summary ?? s.data.data?.summary ?? { income:0, expense:0, balance:0 });
            const labels = t.data.trendData?.labels ?? [];
            setTrend(labels.map((m: string, i: number) => ({
                month: m, income: t.data.trendData.income[i], expense: t.data.trendData.expense[i],
            })));
            setLoading(false);
        })();
    }, []);

    if (loading) return <div>Yükleniyor…</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-5xl mx-auto p-4 space-y-6">
                <h2 className="text-2xl font-bold">Dashboard</h2>
                <DashboardCards income={summary.income} expense={summary.expense} balance={summary.balance} />
                <section>
                    <h3 className="font-semibold mb-2">Aylık Trend</h3>
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
                </section>
            </main>
        </ProtectedRoute>
    );
}

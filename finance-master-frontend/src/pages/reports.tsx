import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer, PieChart, Pie, Cell, Legend } from "recharts";
import toast from "react-hot-toast";

type Trend = { month: string; income: number; expense: number };

const COLORS = ["#0088FE","#FFBB28","#00C49F","#FF8042","#A020F0","#FF6384"];

export default function Reports() {
    const [summary, setSummary] = useState({ income: 0, expense: 0, balance: 0 });
    const [trend, setTrend] = useState<Trend[]>([]);
    const [breakdown, setBreakdown] = useState<{ category: string; total: number }[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        (async () => {
            try {
                const s = await api.get("/reports?period=monthly");
                const t = await api.get("/reports?period=monthly&trend=1");
                setSummary(s.data.summary ?? s.data.data?.summary ?? { income:0, expense:0, balance:0 });
                const labels = t.data.trendData?.labels ?? [];
                setTrend(labels.map((m: string, i: number) => ({
                    month: m,
                    income: t.data.trendData.income[i],
                    expense: t.data.trendData.expense[i],
                })));
                setBreakdown(s.data.categoryBreakdown ?? s.data.data?.categoryBreakdown ?? []);
            } catch {
                toast.error("Raporlar alınamadı");
            } finally {
                setLoading(false);
            }
        })();
    }, []);

    if (loading) return <div>Yükleniyor…</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-5xl mx-auto p-4 space-y-6">
                <h2 className="text-2xl font-bold">Raporlar</h2>

                <section className="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div className="p-4 rounded bg-green-100 dark:bg-green-900/30">Gelir: <b>{summary.income}₺</b></div>
                    <div className="p-4 rounded bg-red-100 dark:bg-red-900/30">Gider: <b>{summary.expense}₺</b></div>
                    <div className="p-4 rounded bg-blue-100 dark:bg-blue-900/30">Bakiye: <b>{summary.balance}₺</b></div>
                </section>

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

                <section>
                    <h3 className="font-semibold mb-2">Kategori Dağılımı (Gider)</h3>
                    <ResponsiveContainer width="100%" height={320}>
                        <PieChart>
                            <Pie data={breakdown} dataKey="total" nameKey="category" cx="50%" cy="50%" outerRadius={110}>
                                {breakdown.map((_, i) => <Cell key={i} fill={COLORS[i % COLORS.length]} />)}
                            </Pie>
                            <Tooltip />
                            <Legend />
                        </PieChart>
                    </ResponsiveContainer>
                </section>
            </main>
        </ProtectedRoute>
    );
}

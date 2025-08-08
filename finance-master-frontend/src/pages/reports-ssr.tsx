import React from "react";
import ProtectedRoute from "../components/ProtectedRoute";
import { GetServerSideProps } from "next";
import { LineChart, Line, CartesianGrid, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";

type Trend = { month: string; income: number; expense: number };

type Props = {
    summary: { income: number; expense: number; balance: number };
    trend: Trend[];
    breakdown: { category: string; total: number }[];
};

export default function ReportsSSR({ summary, trend, breakdown }: Props) {
    return (
        <ProtectedRoute>
            <main className="max-w-5xl mx-auto p-4 space-y-6">
                <h2 className="text-2xl font-bold">Raporlar (SSR)</h2>

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
                    <h3 className="font-semibold mb-2">Kategori Dağılımı</h3>
                    <ul className="space-y-1">
                        {breakdown.map((b, i) => (
                            <li key={i} className="flex justify-between border-b py-1">
                                <span>{b.category}</span><b>{b.total}₺</b>
                            </li>
                        ))}
                    </ul>
                </section>
            </main>
        </ProtectedRoute>
    );
}

export const getServerSideProps: GetServerSideProps<Props> = async (ctx) => {
    const base = process.env.NEXT_PUBLIC_API_BASE_URL || "http://localhost:8000/api";

    const headers: HeadersInit = {};
    if (ctx.req.headers.cookie) headers["cookie"] = ctx.req.headers.cookie;

    const [s, t] = await Promise.all([
        fetch(`${base}/reports?period=monthly`, { headers }),
        fetch(`${base}/reports?period=monthly&trend=1`, { headers }),
    ]);

    if (s.status === 401 || t.status === 401) {
        return { redirect: { destination: "/login", permanent: false } };
    }

    const sd = await s.json();
    const td = await t.json();

    const summary = sd.summary ?? sd.data?.summary ?? { income: 0, expense: 0, balance: 0 };
    const labels: string[] = td.trendData?.labels ?? [];
    const trend = labels.map((m, i) => ({
        month: m,
        income: td.trendData.income[i],
        expense: td.trendData.expense[i],
    }));
    const breakdown = sd.categoryBreakdown ?? sd.data?.categoryBreakdown ?? [];

    return { props: { summary, trend, breakdown } };
};

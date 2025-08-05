import React, { useEffect, useState } from "react";
import api from "../api/axios";
import ProtectedRoute from "../components/ProtectedRoute";
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts";

const Reports = () => {
    const [trend, setTrend] = useState<{ month: string, income: number, expense: number }[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        api.get("/reports?period=monthly")
            .then(res => {
                const data = res.data.data || res.data.trendData || [];
                setTrend(
                    data.labels?.map((month: string, i: number) => ({
                        month,
                        income: data.income[i],
                        expense: data.expense[i],
                    })) || []
                );
            })
            .catch(() => setError("Raporlar alınamadı!"))
            .finally(() => setLoading(false));
    }, []);

    if (loading) return <div>Yükleniyor...</div>;
    return (
        <ProtectedRoute>
            <h2>Trend Raporları</h2>
            {error && <div style={{color:"red"}}>{error}</div>}
            <ResponsiveContainer width="100%" height={300}>
                <LineChart data={trend}>
                    <CartesianGrid strokeDasharray="3 3" />
                    <XAxis dataKey="month" />
                    <YAxis />
                    <Tooltip />
                    <Line type="monotone" dataKey="income" stroke="#4caf50" name="Gelir" />
                    <Line type="monotone" dataKey="expense" stroke="#f44336" name="Gider" />
                </LineChart>
            </ResponsiveContainer>
        </ProtectedRoute>
    );
};

export default Reports;

import React, { useEffect, useState } from "react";
import api from "../api/axios";
import { PieChart, Pie, Cell, Tooltip, ResponsiveContainer, Legend } from "recharts";
import ProtectedRoute from "../components/ProtectedRoute";

const COLORS = ["#0088FE", "#FFBB28", "#00C49F", "#FF8042", "#A020F0", "#FF6384"];

const CategoryPie = () => {
    const [data, setData] = useState<{ category: string; total: number }[]>([]);
    useEffect(() => {
        api.get("/reports?period=monthly").then(res =>
            setData(res.data.data?.categoryBreakdown || [])
        );
    }, []);
    return (
        <ProtectedRoute>
            <h3 className="font-bold mb-3">Giderlerin Kategori Dağılımı</h3>
            <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                    <Pie data={data} dataKey="total" nameKey="category" cx="50%" cy="50%" outerRadius={100}>
                        {data.map((entry, i) => (
                            <Cell key={i} fill={COLORS[i % COLORS.length]} />
                        ))}
                    </Pie>
                    <Tooltip />
                    <Legend />
                </PieChart>
            </ResponsiveContainer>
        </ProtectedRoute>
    );
};
export default CategoryPie;

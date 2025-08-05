import React, { useEffect, useState } from "react";
import api from "../api/axios";
import toast from "react-hot-toast";
import ProtectedRoute from "../components/ProtectedRoute";

type Budget = {
    id: number;
    category_id: number;
    category: { name: string };
    amount: number;
    month: string;
};

type Category = { id: number; name: string };

const Budgets = () => {
    const [budgets, setBudgets] = useState<Budget[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [loading, setLoading] = useState(true);
    const [editId, setEditId] = useState<number | null>(null);
    const [form, setForm] = useState<{ category_id: number; amount: number; month: string }>({
        category_id: 0,
        amount: 0,
        month: "",
    });

    useEffect(() => {
        Promise.all([
            api.get("/budgets"),
            api.get("/categories"),
        ])
            .then(([b, c]) => {
                setBudgets(b.data.data); setCategories(c.data.data);
            })
            .catch(() => toast.error("Veriler alınamadı!"))
            .finally(() => setLoading(false));
    }, []);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm(f => ({ ...f, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!form.category_id || !form.amount || !form.month) {
            toast.error("Tüm alanlar zorunlu!"); return;
        }
        try {
            if (editId) {
                const res = await api.put(`/budgets/${editId}`, form);
                setBudgets(budgets.map(b => (b.id === editId ? res.data.data : b)));
                toast.success("Bütçe güncellendi!");
            } else {
                const res = await api.post("/budgets", form);
                setBudgets([...budgets, res.data.data]);
                toast.success("Bütçe eklendi!");
            }
            setForm({ category_id: 0, amount: 0, month: "" });
            setEditId(null);
        } catch {
            toast.error(editId ? "Güncellenemedi!" : "Eklenemedi!");
        }
    };

    const handleDelete = async (id: number) => {
        try {
            await api.delete(`/budgets/${id}`);
            setBudgets(budgets.filter(b => b.id !== id));
            toast.success("Bütçe silindi!");
        } catch {
            toast.error("Silinemedi!");
        }
    };

    const startEdit = (b: Budget) => {
        setForm({
            category_id: b.category_id,
            amount: b.amount,
            month: b.month.slice(0, 7),
        });
        setEditId(b.id);
    };

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <ProtectedRoute>
            <h2 className="text-xl font-bold mb-2">Bütçeler</h2>
            <table className="w-full mb-4 border">
                <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Miktar</th>
                    <th>Ay</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {budgets.map(b => (
                    <tr key={b.id}>
                        <td>{b.category?.name}</td>
                        <td>{b.amount}</td>
                        <td>{b.month?.slice(0, 7)}</td>
                        <td>
                            <button onClick={() => startEdit(b)} className="px-2 text-blue-600">Düzenle</button>
                            <button onClick={() => handleDelete(b.id)} className="px-2 text-red-600">Sil</button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
            <form onSubmit={handleSubmit} className="flex gap-2 items-center mb-4">
                <select
                    name="category_id"
                    value={form.category_id}
                    onChange={handleChange}
                    className="border rounded p-1"
                    required
                >
                    <option value="">Kategori seç...</option>
                    {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
                <input
                    type="number"
                    name="amount"
                    value={form.amount || ""}
                    onChange={handleChange}
                    min={0.01}
                    step={0.01}
                    placeholder="Miktar"
                    className="border rounded p-1"
                    required
                />
                <input
                    type="month"
                    name="month"
                    value={form.month}
                    onChange={handleChange}
                    className="border rounded p-1"
                    required
                />
                <button type="submit" className="bg-blue-600 text-white rounded px-3 py-1">
                    {editId ? "Güncelle" : "Ekle"}
                </button>
                {editId && (
                    <button type="button" className="ml-2 text-gray-500" onClick={() => { setEditId(null); setForm({ category_id: 0, amount: 0, month: "" }); }}>
                        İptal
                    </button>
                )}
            </form>
        </ProtectedRoute>
    );
};

export default Budgets;

import React, { useEffect, useState } from "react";
import api from "../api/axios";
import toast from "react-hot-toast";
import ProtectedRoute from "../components/ProtectedRoute";

type Category = { id: number; name: string };
type Transaction = {
    id: number;
    type: string;
    amount: number;
    date: string;
    description: string;
    category: { id: number; name: string } | null;
};

const Transactions = () => {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [form, setForm] = useState({
        category_id: "",
        type: "expense",
        amount: "",
        date: "",
        description: "",
    });
    const [editId, setEditId] = useState<number | null>(null);
    const [loading, setLoading] = useState(true);

    // İlk veri çekme
    useEffect(() => {
        Promise.all([api.get("/transactions"), api.get("/categories")])
            .then(([t, c]) => {
                setTransactions(t.data.data || []);
                setCategories(c.data.data || []);
            })
            .catch(() => toast.error("Veriler alınamadı!"))
            .finally(() => setLoading(false));
    }, []);

    // Input değişimi
    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setForm(f => ({ ...f, [e.target.name]: e.target.value }));
    };

    // Kaydet/güncelle
    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!form.category_id || !form.amount || !form.date) {
            toast.error("Tüm alanlar zorunlu!"); return;
        }
        try {
            if (editId) {
                const res = await api.put(`/transactions/${editId}`, { ...form, amount: +form.amount });
                setTransactions(transactions.map(t => t.id === editId ? res.data.data : t));
                toast.success("İşlem güncellendi!");
                setEditId(null);
            } else {
                const res = await api.post("/transactions", { ...form, amount: +form.amount });
                setTransactions([...transactions, res.data.data]);
                toast.success("İşlem eklendi!");
            }
            setForm({ category_id: "", type: "expense", amount: "", date: "", description: "" });
        } catch {
            toast.error(editId ? "İşlem güncellenemedi!" : "İşlem eklenemedi!");
        }
    };

    // Silme
    const handleDelete = async (id: number) => {
        if (!window.confirm("Silmek istediğine emin misin?")) return;
        try {
            await api.delete(`/transactions/${id}`);
            setTransactions(transactions.filter(t => t.id !== id));
            toast.success("İşlem silindi!");
        } catch {
            toast.error("İşlem silinemedi!");
        }
    };

    // Düzenleme
    const startEdit = (t: Transaction) => {
        setForm({
            category_id: t.category?.id ? String(t.category.id) : "",
            type: t.type,
            amount: t.amount + "",
            date: t.date.slice(0, 10),
            description: t.description || "",
        });
        setEditId(t.id);
    };

    if (loading) return <div>Yükleniyor...</div>;

    return (
        <ProtectedRoute>
            <main className="max-w-lg mx-auto p-4">
                <h2 className="text-lg font-bold mb-2">Gelir-Gider İşlemleri</h2>
                <ul className="mb-4">
                    {transactions.map(t => (
                        <li key={t.id} className="flex justify-between items-center py-1 border-b">
              <span>
                [{t.type === "income" ? "+" : "-"}] {t.amount}₺ | {t.category?.name || "-"} | {t.date?.slice(0, 10)} | {t.description}
              </span>
                            <div>
                                <button className="text-blue-500 mr-2" onClick={() => startEdit(t)}>Düzenle</button>
                                <button className="text-red-500" onClick={() => handleDelete(t.id)}>Sil</button>
                            </div>
                        </li>
                    ))}
                </ul>
                <form onSubmit={handleSubmit} className="flex flex-col gap-2">
                    <select name="category_id" value={form.category_id} onChange={handleChange} className="border rounded p-1" required>
                        <option value="">Kategori seç...</option>
                        {categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                    </select>
                    <select name="type" value={form.type} onChange={handleChange} className="border rounded p-1">
                        <option value="income">Gelir</option>
                        <option value="expense">Gider</option>
                    </select>
                    <input name="amount" value={form.amount} onChange={handleChange} type="number" min={0.01} placeholder="Miktar" required className="border rounded p-1" />
                    <input name="date" value={form.date} onChange={handleChange} type="date" required className="border rounded p-1" />
                    <input name="description" value={form.description} onChange={handleChange} placeholder="Açıklama" className="border rounded p-1" />
                    <div className="flex gap-2">
                        <button type="submit" className="bg-blue-600 text-white px-4 rounded">{editId ? "Güncelle" : "Ekle"}</button>
                        {editId && <button type="button" onClick={() => { setEditId(null); setForm({ category_id: "", type: "expense", amount: "", date: "", description: "" }); }} className="text-gray-500">İptal</button>}
                    </div>
                </form>
            </main>
        </ProtectedRoute>
    );
};
export default Transactions;

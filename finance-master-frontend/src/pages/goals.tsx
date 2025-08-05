import React, { useEffect, useState } from "react";
import api from "../api/axios";
import toast from "react-hot-toast";
import ProtectedRoute from "../components/ProtectedRoute";

type Goal = { id: number; title: string; target_amount: number; current_amount: number; end_date: string };

const Goals = () => {
    const [goals, setGoals] = useState<Goal[]>([]);
    const [form, setForm] = useState({ title: "", target_amount: "", current_amount: "", end_date: "" });
    const [editId, setEditId] = useState<number | null>(null);

    useEffect(() => {
        api.get("/goals")
            .then(res => setGoals(res.data.data))
            .catch(() => toast.error("Hedefler alınamadı!"));
    }, []);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm(f => ({ ...f, [e.target.name]: e.target.value }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            if (editId) {
                const res = await api.put(`/goals/${editId}`, { ...form, target_amount: +form.target_amount, current_amount: +form.current_amount });
                setGoals(goals.map(g => g.id === editId ? res.data.data : g));
                toast.success("Hedef güncellendi!");
                setEditId(null);
            } else {
                const res = await api.post("/goals", { ...form, target_amount: +form.target_amount, current_amount: +form.current_amount });
                setGoals([...goals, res.data.data]);
                toast.success("Hedef eklendi!");
            }
            setForm({ title: "", target_amount: "", current_amount: "", end_date: "" });
        } catch { toast.error("İşlem başarısız!"); }
    };

    const handleDelete = async (id: number) => {
        if (!window.confirm("Silmek istediğine emin misin?")) return;
        try {
            await api.delete(`/goals/${id}`);
            setGoals(goals.filter(g => g.id !== id));
            toast.success("Hedef silindi!");
        } catch { toast.error("Hedef silinemedi!"); }
    };

    return (
        <ProtectedRoute>
            <main className="max-w-lg mx-auto p-4">
                <h2 className="text-lg font-bold mb-2">Hedefler</h2>
                <ul className="mb-4">
                    {goals.map(g => (
                        <li key={g.id} className="flex justify-between items-center py-1">
                            <span>{g.title} | {g.current_amount}/{g.target_amount}₺ | {g.end_date?.slice(0,10)}</span>
                            <div>
                                <button className="text-blue-500 mr-2" onClick={() => { setEditId(g.id); setForm({
                                    title: g.title, target_amount: g.target_amount + "", current_amount: g.current_amount + "", end_date: g.end_date.slice(0,10)
                                }); }}>Düzenle</button>
                                <button className="text-red-500" onClick={() => handleDelete(g.id)}>Sil</button>
                            </div>
                        </li>
                    ))}
                </ul>
                <form onSubmit={handleSubmit} className="flex flex-col gap-2">
                    <input name="title" value={form.title} onChange={handleChange} placeholder="Hedef başlığı" required className="border rounded p-1" />
                    <input name="target_amount" value={form.target_amount} onChange={handleChange} type="number" min={0.01} placeholder="Hedef miktar" required className="border rounded p-1" />
                    <input name="current_amount" value={form.current_amount} onChange={handleChange} type="number" min={0} placeholder="Başlangıç miktar" className="border rounded p-1" />
                    <input name="end_date" value={form.end_date} onChange={handleChange} type="date" required className="border rounded p-1" />
                    <div className="flex gap-2">
                        <button type="submit" className="bg-blue-600 text-white px-4 rounded">{editId ? "Güncelle" : "Ekle"}</button>
                        {editId && <button type="button" onClick={() => { setEditId(null); setForm({ title: "", target_amount: "", current_amount: "", end_date: "" }); }} className="text-gray-500">İptal</button>}
                    </div>
                </form>
            </main>
        </ProtectedRoute>
    );
};
export default Goals;

import React, { useEffect, useState } from "react";
import api from "../api/axios";
import toast from "react-hot-toast";
import ProtectedRoute from "../components/ProtectedRoute";

type Category = { id: number; name: string };

const Categories = () => {
    const [categories, setCategories] = useState<Category[]>([]);
    const [name, setName] = useState("");
    const [editId, setEditId] = useState<number | null>(null);

    useEffect(() => {
        api.get("/categories")
            .then(res => setCategories(res.data.data || res.data))
            .catch(() => toast.error("Kategoriler alınamadı!"));
    }, []);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!name.trim()) { toast.error("Kategori adı zorunlu!"); return; }
        try {
            if (editId) {
                const res = await api.put(`/categories/${editId}`, { name });
                setCategories(categories.map(c => c.id === editId ? res.data.data : c));
                setEditId(null);
                toast.success("Kategori güncellendi!");
            } else {
                const res = await api.post("/categories", { name });
                setCategories([...categories, res.data.data]);
                toast.success("Kategori eklendi!");
            }
            setName("");
        } catch {
            toast.error(editId ? "Güncelleme başarısız!" : "Ekleme başarısız!");
        }
    };

    const handleDelete = async (id: number) => {
        if (!window.confirm("Silmek istediğinize emin misiniz?")) return;
        try {
            await api.delete(`/categories/${id}`);
            setCategories(categories.filter(c => c.id !== id));
            toast.success("Kategori silindi!");
        } catch {
            toast.error("Silinemedi!");
        }
    };

    const startEdit = (c: Category) => {
        setName(c.name);
        setEditId(c.id);
    };

    return (
        <ProtectedRoute>
            <div className="max-w-lg mx-auto p-4">
                <h2 className="text-lg font-bold mb-3">Kategoriler</h2>
                <ul className="mb-4">
                    {categories.map(c => (
                        <li key={c.id} className="flex justify-between items-center border-b py-2">
                            <span>{c.name}</span>
                            <div>
                                <button className="text-blue-500 mr-2" onClick={() => startEdit(c)}>Düzenle</button>
                                <button className="text-red-500" onClick={() => handleDelete(c.id)}>Sil</button>
                            </div>
                        </li>
                    ))}
                </ul>
                <form onSubmit={handleSubmit} className="flex gap-2">
                    <input
                        value={name}
                        onChange={e => setName(e.target.value)}
                        placeholder="Kategori adı"
                        className="border rounded p-2 flex-1"
                        maxLength={32}
                        required
                    />
                    <button type="submit" className="bg-blue-600 text-white px-4 rounded">
                        {editId ? "Güncelle" : "Ekle"}
                    </button>
                    {editId && (
                        <button type="button" onClick={() => { setEditId(null); setName(""); }} className="text-gray-500">
                            İptal
                        </button>
                    )}
                </form>
            </div>
        </ProtectedRoute>
    );
};
export default Categories;

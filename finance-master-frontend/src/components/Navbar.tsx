import React, { useState } from "react";
import Link from "next/link";
import { Menu, X } from "lucide-react";
import { useAuth } from "../context/AuthContext";
import ProfileAvatar from "./ProfileAvatar";

const navLinks = [
    { href: "/", label: "Dashboard" },
    { href: "/transactions", label: "İşlemler" },
    { href: "/categories", label: "Kategoriler" },
    { href: "/budgets", label: "Bütçeler" },
    { href: "/goals", label: "Hedefler" },
    { href: "/notifications", label: "Bildirimler" },
    { href: "/profile", label: "Profil" },
];

const auth = useAuth();
const user = auth?.user;
const Navbar: React.FC = () => {
    const [open, setOpen] = useState(false);
    const { user } = useAuth() || {};

    return (
        <nav className="w-full bg-blue-600 text-white px-4 py-2 flex items-center justify-between relative">
            <span className="font-bold text-xl">Finance Master</span>
            <div className="md:hidden">
                <button onClick={() => setOpen(o => !o)}>
                    {open ? <X size={28} /> : <Menu size={28} />}
                </button>
            </div>
            <ul className={`
                md:flex gap-4 items-center
                ${open ? "block" : "hidden"}
                absolute md:static top-14 left-0 right-0 bg-blue-700 md:bg-transparent p-4 md:p-0 z-50
            `}>
                {navLinks.map(link => (
                    <li key={link.href} className="mb-2 md:mb-0">
                        <Link href={link.href} className="hover:underline" onClick={() => setOpen(false)}>
                            {link.label}
                        </Link>
                    </li>
                ))}
                {user && (
                    <li className="ml-2">
                        <ProfileAvatar user={user} size={36} />
                    </li>
                )}
                {/* <li><ThemeSwitch /></li>  // Temalı navbar için aktif et */}
            </ul>
        </nav>
    );
};
export default Navbar;

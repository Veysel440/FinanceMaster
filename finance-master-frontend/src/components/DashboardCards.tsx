import React from "react";

export default function DashboardCards({
                                           income, expense, balance,
                                       }: { income: number; expense: number; balance: number }) {
    return (
        <section className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div className="p-4 rounded shadow-sm bg-white dark:bg-gray-800">
                <div className="text-sm opacity-70">Gelir</div>
                <div className="text-2xl font-bold text-green-600 dark:text-green-400">{income}₺</div>
            </div>
            <div className="p-4 rounded shadow-sm bg-white dark:bg-gray-800">
                <div className="text-sm opacity-70">Gider</div>
                <div className="text-2xl font-bold text-red-600 dark:text-red-400">{expense}₺</div>
            </div>
            <div className="p-4 rounded shadow-sm bg-white dark:bg-gray-800">
                <div className="text-sm opacity-70">Bakiye</div>
                <div className="text-2xl font-bold text-blue-600 dark:text-blue-400">{balance}₺</div>
            </div>
        </section>
    );
}

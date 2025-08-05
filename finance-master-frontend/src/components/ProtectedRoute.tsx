import { useAuth } from "../context/AuthContext";
import { useRouter } from "next/router";
import React, { useEffect } from "react";

const ProtectedRoute: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const { user, loading } = useAuth();
    const router = useRouter();

    useEffect(() => {
        if (!loading && !user) {
            router.replace("/login");
        }
    }, [user, loading, router]);

    if (loading || !user) return <div>Yükleniyor...</div>;
    return <>{children}</>;
};
export default ProtectedRoute;

import type { AppProps } from "next/app";
import "../styles/globals.css";
import { AuthProvider } from "../context/AuthContext";
import Navbar from "../components/Navbar";
import { Toaster } from "react-hot-toast";

function MyApp({ Component, pageProps }: AppProps) {
    return (
        <AuthProvider>
            <Navbar />
            <main className="min-h-screen bg-gray-50">
                <Component {...pageProps} />
            </main>
            <Toaster position="top-center" />
        </AuthProvider>
    );
}

export default MyApp;

import type { AppProps } from "next/app";
import { appWithTranslation } from "next-i18next";
import "../styles/globals.css";
import { AuthProvider } from "../context/AuthContext";
import Navbar from "../components/Navbar";
import { Toaster } from "react-hot-toast";

function MyApp({ Component, pageProps }: AppProps) {
    return (
        <AuthProvider>
            <Navbar />
            <main className="min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                <Component {...pageProps} />
            </main>
            <Toaster position="top-center" />
        </AuthProvider>
    );
}

export default appWithTranslation(MyApp);

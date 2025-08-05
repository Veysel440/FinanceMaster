import React from "react";
import Navbar from "./Navbar";

const Layout: React.FC<{children: React.ReactNode}> = ({ children }) => (
    <>
        <Navbar />
        <main style={{ maxWidth: 960, margin: "32px auto" }}>
            {children}
        </main>
    </>
);
export default Layout;

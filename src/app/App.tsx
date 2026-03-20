import { ThemeProvider } from './context/ThemeContext';
import { BrowserRouter, Routes, Route } from "react-router-dom";

import { Header } from './components/Header';
import { Footer } from './components/Footer';

import StockPage from "../pages/StockPage";
import HomePage from "../pages/HomePage"; // we will create this
import VehiclePage from "../pages/VehiclePage"; // we will create this

export default function App() {
  return (
    <ThemeProvider>
      <BrowserRouter>
        <div className="min-h-screen bg-white dark:bg-gray-900 transition-colors">
          <Header />

          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/stock" element={<StockPage />} />
            <Route path="/vehicle/:id" element={<VehiclePage />} />
          </Routes>

          <Footer />
        </div>
      </BrowserRouter>
    </ThemeProvider>
  );
}

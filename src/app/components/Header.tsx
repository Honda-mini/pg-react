import { Moon, Sun, Menu, X } from 'lucide-react';
import { useTheme } from '../context/ThemeContext';
import { useState } from 'react';
import Logo from '../components/Logo';

export function Header() {
  const { theme, toggleTheme } = useTheme();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
      <div className="mx-auto px-4 sm:px-6 lg:px-8 max-w-screen-xl">
        <div className="flex items-center justify-between h-16">

<Logo variant="full" />

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-6">
            <a href="/stock" className="nav-link">Inventory</a>
            <a href="#about" className="nav-link">About</a>
            <a href="#contact" className="nav-link">Contact</a>

            <button
              onClick={toggleTheme}
              className="p-2 rounded-md bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
              aria-label="Toggle theme"
            >
              {theme === 'light'
                ? <Moon className="w-5 h-5 text-gray-700" />
                : <Sun className="w-5 h-5 text-yellow-500" />}
            </button>
          </nav>

          {/* Mobile Controls */}
          <div className="flex md:hidden items-center gap-2">
            <button
              onClick={toggleTheme}
              className="p-2 rounded-md bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
              aria-label="Toggle theme"
            >
              {theme === 'light'
                ? <Moon className="w-5 h-5 text-gray-700" />
                : <Sun className="w-5 h-5 text-yellow-500" />}
            </button>

            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="p-2 rounded-md bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
              aria-label="Toggle menu"
            >
              {mobileMenuOpen
                ? <X className="w-6 h-6 text-gray-700 dark:text-gray-300" />
                : <Menu className="w-6 h-6 text-gray-700 dark:text-gray-300" />}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        {mobileMenuOpen && (
          <nav className="md:hidden py-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 animate-in fade-in slide-in-from-top-2 duration-200">
            <div className="flex flex-col gap-3">
              <a href="#inventory" className="mobile-link" onClick={() => setMobileMenuOpen(false)}>Inventory</a>
              <a href="#financing" className="mobile-link" onClick={() => setMobileMenuOpen(false)}>Financing</a>
              <a href="#about" className="mobile-link" onClick={() => setMobileMenuOpen(false)}>About</a>
              <a href="#contact" className="mobile-link" onClick={() => setMobileMenuOpen(false)}>Contact</a>
            </div>
          </nav>
        )}
      </div>
    </header>
  );
}

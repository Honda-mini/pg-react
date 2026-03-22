import { Facebook, Twitter, Instagram, Linkedin, Mail, Phone, MapPin } from 'lucide-react';
import Logo from '../components/Logo';


export function Footer() {
  return (
    <footer className="bg-gray-900 dark:bg-black text-white pt-16 pb-8" id="contact">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          {/* Company Info */}
          <div>
<Logo variant="footer" />
            <p className="text-gray-400 mb-6">
              Your trusted partner in finding the perfect vehicle. Premium quality, exceptional service.
            </p>
            <div className="flex gap-4">
              <a href="#" className="w-10 h-10 bg-gray-800 dark:bg-gray-900 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 transition-colors">
                <Facebook className="w-5 h-5" />
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 dark:bg-gray-900 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 transition-colors">
                <Twitter className="w-5 h-5" />
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 dark:bg-gray-900 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 transition-colors">
                <Instagram className="w-5 h-5" />
              </a>
              <a href="#" className="w-10 h-10 bg-gray-800 dark:bg-gray-900 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 transition-colors">
                <Linkedin className="w-5 h-5" />
              </a>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Quick Links</h3>
            <ul className="space-y-3">
              <li><a href="/stock" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Browse Inventory</a></li>
              <li><a href="#financing" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Financing Options</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Trade-In Value</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Warranty Info</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Schedule Service</a></li>
            </ul>
          </div>

          {/* Services */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Services</h3>
            <ul className="space-y-3">
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">New Cars</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Pre-Owned Cars</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Luxury Vehicles</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Electric Vehicles</a></li>
              <li><a href="#" className="text-gray-400 hover:text-blue-600 dark:hover:text-yellow-500 transition-colors">Custom Orders</a></li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Contact Us</h3>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-blue-600 dark:text-yellow-500 mt-1 flex-shrink-0" />
                <span className="text-gray-400">
                  Chywoone Hill<br/> Newlyn<br/>Penzance TR18 5AR
                </span>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-blue-600 dark:text-yellow-500 flex-shrink-0" />
                <span className="text-gray-400">Phone : 01736 369940<br/>Mobile : 07887653155</span>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-blue-600 dark:text-yellow-500 flex-shrink-0" />
                <span className="text-gray-400">paul.pgservices@gmail.com</span>
              </li>
            </ul>
          </div>
        </div>

        <div className="pt-8 border-t border-gray-800 dark:border-gray-900 text-center text-gray-400">
          <p>&copy; 2026 PG Services. All rights reserved. | Privacy Policy | Terms of Service</p>
        </div>
      </div>
    </footer>
  );
}

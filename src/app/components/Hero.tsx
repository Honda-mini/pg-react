import { ArrowRight } from 'lucide-react';

// ===============================
// HERO COMPONENT
// ===============================
export function Hero() {
  return (
    // ===============================
    // HERO SECTION WRAPPER (OUTSIDE JSX COMMENT)
    // ===============================

    <section className="relative h-[70vh] min-h-[500px] w-full flex items-center justify-center overflow-hidden">

      {/* ===============================
          OVERLAY (INSIDE JSX COMMENT)
          Controls darkness, gradient, theme behaviour
         =============================== */}
      <div className="absolute inset-0 bg-gradient-to-r from-blue-600/40 to-blue-800/40 dark:from-gray-900/40 dark:to-gray-900/45 z-10" />

      {/* ===============================
          BACKGROUND IMAGE
          Change the hero image here
         =============================== */}
      <img
        src="/images/megane.jpg"
        alt="Luxury car showroom"
        className="absolute inset-0 w-full h-full object-cover"
      />

      {/* ===============================
          HERO CONTENT WRAPPER
          Centers text + buttons above overlay
         =============================== */}
<div className="relative z-20 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        {/* ===============================
            HERO TITLE
           =============================== */}
        <h1 className="text-5xl md:text-6xl lg:text-7xl font-bold text-white mb-6">
          Quality Cars, Trusted Locally in Cornwall
        </h1>

        {/* ===============================
            HERO SUBTITLE
           =============================== */}
        <p className="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
          Proudly serving Cornwall with carefully selected vehicles, honest advice, and a reputation built on trust.
        </p>

        {/* ===============================
            HERO BUTTONS
           =============================== */}
        <div className="flex flex-col sm:flex-row gap-4 items-stretch sm:items-center justify-center">

          {/* Primary button */}
         <a href="/stock"
              className="w-full sm:w-auto px-8 py-4 bg-white dark:bg-yellow-500 text-blue-600 dark:text-gray-900 rounded-lg hover:bg-gray-100 dark:hover:bg-yellow-400 transition-colors flex items-center justify-center gap-2 group">
                Browse Inventory
            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
        </a>
          {/* Secondary button */}
        <a href="mailto:info@pgservices.co.uk?subject=Test%20drive%20Booking&body=Hi%20Paul,%0D%0A%0D%0AI'd%20like%20to%20look%20at%20booking%20a%20test%20drive.%0D%0A%0D%0AThanks!"
             className="w-full sm:w-auto px-8 py-4 bg-transparent border-2 border-white text-white rounded-lg hover:bg-white/10 transition-colors flex items-center justify-center">
                Schedule Test Drive
        </a>

        </div>
      </div>
    </section>
  );
}

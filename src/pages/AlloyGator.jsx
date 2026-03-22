import React from "react";

export default function AlloyGatorPage() {
  return (
    <div className="max-w-5xl mx-auto px-4 py-10 space-y-12">

      {/* Hero */}
      <section className="space-y-4 text-center">
        <h1 className="text-4xl font-bold text-gray-900 dark:text-white">
          AlloyGator Rim Protection
        </h1>
        <p className="text-lg text-gray-600 dark:text-gray-300">
          Premium wheel protection professionally installed in Penzance.
        </p>
      </section>

      {/* What is AlloyGator */}
      <section className="space-y-4">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          What Is AlloyGator?
        </h2>
        <p className="text-gray-700 dark:text-gray-300 leading-relaxed">
          AlloyGator is a tough, precision‑engineered nylon ring that sits between your tyre and alloy wheel.
          It absorbs kerb impact, prevents cosmetic damage, and keeps your wheels looking sharp for longer.
        </p>
      </section>

      {/* Benefits */}
      <section className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Why Choose AlloyGator?
        </h2>

        <div className="grid sm:grid-cols-2 gap-6">
          {[
            "Kerb protection from scrapes and scuffs",
            "Cheaper than a full wheel refurb",
            "OEM‑style clean fitment",
            "Durable nylon construction",
            "Multiple colour options",
            "Professional installation in Penzance",
          ].map((item, i) => (
            <div key={i} className="p-4 rounded-lg bg-gray-100 dark:bg-gray-800">
              <p className="text-gray-800 dark:text-gray-200">{item}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Colours */}
      <section className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Colour Options
        </h2>

        <div className="grid grid-cols-2 sm:grid-cols-3 gap-4">
          {[
            "Black",
            "Graphite",
            "Silver",
            "Red",
            "Blue",
            "Yellow",
            "Orange",
            "Green",
            "Purple",
          ].map((colour, i) => (
            <div
              key={i}
              className="p-3 rounded-lg bg-gray-100 dark:bg-gray-800 text-center"
            >
              <p className="text-gray-800 dark:text-gray-200">{colour}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Pricing */}
      <section className="space-y-4">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Pricing
        </h2>
        <p className="text-gray-700 dark:text-gray-300 leading-relaxed">
          <strong>From £XX fitted</strong>.  
          Price varies depending on wheel size and colour choice.
        </p>
      </section>

      {/* CTA */}
      <section className="text-center space-y-4">
        <a
          href="/contact"
          className="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
        >
          Book Installation
        </a>
        <p className="text-gray-600 dark:text-gray-400">
          Or message us on WhatsApp for quick availability.
        </p>
      </section>
    </div>
  );
}

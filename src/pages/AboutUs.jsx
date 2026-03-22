import React from "react";

export default function AboutPage() {
  return (
    <div className="max-w-5xl mx-auto px-4 py-10 space-y-12">

      {/* Hero */}
      <section className="space-y-4 text-center">
        <h1 className="text-4xl font-bold text-gray-900 dark:text-white">
          About PG Services
        </h1>
        <p className="text-lg text-gray-600 dark:text-gray-300">
          Independent. Detail‑driven. Customer‑focused.
        </p>
      </section>

      {/* Story */}
      <section className="space-y-4">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Our Story
        </h2>
        <p className="text-gray-700 dark:text-gray-300 leading-relaxed">
          PG Services was built on a simple idea: every vehicle deserves to look its best.
          What began as a passion for detailing and presentation has grown into a boutique
          automotive service offering carefully selected vehicles, premium preparation,
          and a customer experience built on honesty and attention to detail.
        </p>
      </section>

      {/* What we do */}
      <section className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          What We Do
        </h2>

        <div className="grid sm:grid-cols-2 gap-6">
          {[
            "Vehicle Sales – curated, well‑maintained stock",
            "Detailing & Preparation – high‑standard presentation",
            "AlloyGator Installation – professional fitting",
            "Automotive Support – practical help and aftercare",
          ].map((item, i) => (
            <div key={i} className="p-4 rounded-lg bg-gray-100 dark:bg-gray-800">
              <p className="text-gray-800 dark:text-gray-200">{item}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Approach */}
      <section className="space-y-6">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Our Approach
        </h2>

        <div className="grid sm:grid-cols-2 gap-6">
          {[
            "Boutique stock selection",
            "High‑standard preparation",
            "Transparent descriptions",
            "Local, personal service",
          ].map((item, i) => (
            <div key={i} className="p-4 rounded-lg bg-gray-100 dark:bg-gray-800">
              <p className="text-gray-800 dark:text-gray-200">{item}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Behind the scenes */}
      <section className="space-y-4">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Behind the Scenes
        </h2>
        <p className="text-gray-700 dark:text-gray-300 leading-relaxed">
          Every vehicle goes through a full preparation routine including cleaning,
          inspection, photography, and listing. We take the time to present each car
          properly so customers know exactly what they’re buying.
        </p>
      </section>

      {/* Values */}
      <section className="space-y-4">
        <h2 className="text-2xl font-semibold text-gray-900 dark:text-white">
          Our Values
        </h2>
        <ul className="list-disc pl-6 text-gray-700 dark:text-gray-300 space-y-1">
          <li>Quality over quantity</li>
          <li>Honesty over sales talk</li>
          <li>Presentation over shortcuts</li>
          <li>Long‑term reputation over quick wins</li>
        </ul>
      </section>

      {/* CTA */}
      <section className="text-center space-y-4">
        <a
          href="/stock"
          className="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
        >
          View Our Stock
        </a>
      </section>
    </div>
  );
}

import React from "react";
import { cleanText, extractSections } from "./descriptionFormatter"; // adjust path

interface Props {
  raw: string;
}

export function PremiumDescription({ raw }: Props) {
  const cleaned = cleanText(raw || "");
  const s = extractSections(cleaned);

  return (
    <div className="space-y-6 text-gray-800 leading-relaxed">

      {/* Intro */}
      {s.intro && (
        <p className="text-lg">
          {s.intro.trim()}
        </p>
      )}

      {/* Key Features */}
      {s.features.length > 0 && (
        <section>
          <h3 className="font-semibold text-gray-900 mb-2">Key Features</h3>
          <ul className="list-disc list-inside space-y-1">
            {s.features.map((f, i) => (
              <li key={i}>{f}</li>
            ))}
          </ul>
        </section>
      )}

      {/* Included at Asking Price */}
      {s.priceIncludes.length > 0 && (
        <section>
          <h3 className="font-semibold text-gray-900 mb-2">
            Included at Asking Price
          </h3>
          <ul className="list-disc list-inside space-y-1">
            {s.priceIncludes.map((f, i) => (
              <li key={i}>{f}</li>
            ))}
          </ul>
        </section>
      )}

      {/* Dealer Information */}
      {s.dealerInfo && (
        <section>
          <h3 className="font-semibold text-gray-900 mb-2">
            Dealer Information
          </h3>
          <p>{s.dealerInfo.trim()}</p>
        </section>
      )}

      {/* Contact */}
      {s.contact && (
        <section>
          <h3 className="font-semibold text-gray-900 mb-2">Contact</h3>
          <p>{s.contact.trim()}</p>
        </section>
      )}
    </div>
  );
}

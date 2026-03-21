// descriptionFormatter.tsx
export function cleanText(text: string) {
  return text
    .normalize("NFKD")
    // keep letters, numbers, punctuation, £, ✓, ✅, spaces
    .replace(/[^a-zA-Z0-9£.,!?\-\/&\s✓✅]/g, " ")
    // collapse multiple ? (emoji remnants)
    .replace(/\?{2,}/g, " ")
    // collapse multiple spaces
    .replace(/\s{2,}/g, " ")
    // tidy commas
    .replace(/\s*,\s*/g, ", ")
    // normalize ALL CAPS words
    .replace(/\b([A-Z]{2,})\b/g, (m) => m[0].toUpperCase() + m.slice(1).toLowerCase())
    .trim();
}

export interface DescriptionSections {
  intro: string[];
  features: string[];
  priceIncludes: string[];
  dealerInfo: string[];
  contact: string[];
}

// helper to inject line breaks for grouping
function injectBreaks(text: string) {
  return text
    .replace(/(due in)/gi, "\n$1")
    .replace(/(only \d+k)/gi, "\n$1")
    .replace(/(all at asking price)/gi, "\n$1")
    .replace(/(service)/gi, "\n$1")
    .replace(/(new mot)/gi, "\n$1")
    .replace(/(cam belt)/gi, "\n$1")
    .replace(/(wax oyl)/gi, "\n$1")
    .replace(/(hpi)/gi, "\n$1")
    .replace(/(valet|valeted)/gi, "\n$1")
    .replace(/(extras inc)/gi, "\n$1")
    .replace(/(this car must be seen)/gi, "\n$1")
    .replace(/(phone|email|website)/gi, "\n$1")
    .replace(/(px poss|part exchange)/gi, "\n$1")
    .replace(/(viewing)/gi, "\n$1")
    .replace(/(open for sales)/gi, "\n$1")
    .replace(/(\d{4}\/\d{2})/g, "\n$1")
    .replace(/(£\d+)/g, "\n$1");
}

export function extractSections(text: string): DescriptionSections {
  const sections: DescriptionSections = {
    intro: [],
    features: [],
    priceIncludes: [],
    dealerInfo: [],
    contact: [],
  };

  const cleanedText = injectBreaks(cleanText(text));
  const lines = cleanedText
    .split(/\r?\n/)
    .map((l) => l.trim())
    .filter(Boolean);

  for (const line of lines) {
    const lower = line.toLowerCase();

    // Features
    if (
      lower.includes("facelift") ||
      lower.includes("extras inc") ||
      lower.includes("gt line") ||
      lower.includes("sat nav") ||
      lower.includes("air con") ||
      lower.includes("cruise") ||
      lower.includes("alloys") ||
      lower.includes("isofix") ||
      lower.includes("multi air bags") ||
      lower.includes("seats") ||
      lower.includes("digital climate") ||
      lower.includes("remote controls")
    ) {
      sections.features.push(line);
      continue;
    }

    // Price Includes
    if (
      lower.startsWith("service") ||
      lower.includes("new mot") ||
      lower.includes("cam belt") ||
      lower.includes("wax oyl") ||
      lower.includes("hpi") ||
      lower.includes("valet") ||
      lower.includes("autoglym") ||
      lower.includes("warranty") ||
      lower.includes("3 months") ||
      lower.includes("3000 mile")
    ) {
      sections.priceIncludes.push(line);
      continue;
    }

    // Contact
    if (lower.includes("phone") || lower.includes("email") || lower.includes("website") || lower.includes("www.")) {
      sections.contact.push(line);
      continue;
    }

    // Dealer Info
    if (
      lower.includes("open for sales") ||
      lower.includes("forecourt") ||
      lower.includes("industrial") ||
      lower.includes("viewing") ||
      lower.includes("appointment") ||
      lower.includes("delivery") ||
      lower.includes("loan car") ||
      lower.includes("recovery") ||
      lower.includes("servicing") ||
      lower.includes("px") ||
      lower.includes("cards") ||
      lower.includes("years motor trade")
    ) {
      sections.dealerInfo.push(line);
      continue;
    }

    // Everything else → intro
    sections.intro.push(line);
  }

  return sections;
}
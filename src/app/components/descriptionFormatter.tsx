// 1. Clean raw dealer text
export function cleanText(text: string) {
  return text
    // normalise corrupted Unicode into real characters
    .normalize("NFKD")

    // remove anything that is NOT:
    // - letters
    // - numbers
    // - punctuation
    // - £
    // - spaces
    .replace(/[^a-zA-Z0-9£.,!?\-\/&\s]/g, " ")

    // collapse multiple ? (emoji remnants)
    .replace(/\?{2,}/g, " ")

    // collapse multiple spaces
    .replace(/\s{2,}/g, " ")

    // tidy commas
    .replace(/\s*,\s*/g, ", ")

    // fix ALL CAPS words (but keep acronyms)
    .replace(/\b([A-Z]{4,})\b/g, (m) => m.toLowerCase())

    .trim();
}

export interface DescriptionSections {
  intro: string;
  features: string[];
  priceIncludes: string[];
  dealerInfo: string;
  contact: string;
}
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
    intro: "",
    features: [],
    priceIncludes: [],
    dealerInfo: "",
    contact: "",
  };

  const lines = text
    .split(/\r?\n/)
    .map((l) => l.trim())
    .filter(Boolean);

  for (const line of lines) {
    const lower = line.toLowerCase();

    // -----------------------------
    // INTRO — short, punchy, car‑focused
    // -----------------------------
    if (
      lower.startsWith("check this out") ||
      lower.startsWith("due in") ||
      lower.includes("being refurbished") ||
      lower.startsWith("just in") ||
      lower.includes("stunning looking") ||
      lower.includes("stunning") ||
      (lower.includes("only") && lower.includes("k")) ||
      lower.includes("great spec") ||
      lower.includes("money saving") ||
      lower.includes("affordable") ||
      lower.includes("cheap running") ||
      lower.includes("cheap road tax") ||
      lower.includes("outstanding mpg") ||
      lower.includes("great value") ||
      lower.includes("only £")
    ) {
      sections.intro += line + " ";
      continue;
    }

    // -----------------------------
    // INCLUDED AT ASKING PRICE — dealer-provided items only
    // -----------------------------
    if (
      lower.startsWith("all at asking price") ||
      lower.startsWith("all at full asking price")
    ) {
      continue; // skip header
    }

    if (
      lower.startsWith("service") ||
      lower.includes("full service") ||
      lower.includes("service if due") ||
      lower.includes("new mot") ||
      lower.includes("cam belt") ||
      lower.includes("wax oyl") ||
      lower.includes("waxoyl") ||
      lower.includes("hpi") ||
      lower.includes("valet") ||
      lower.includes("valeted") ||
      lower.includes("autoglym") ||
      lower.includes("warranty") ||
      lower.includes("3 months") ||
      lower.includes("3000 mile")
    ) {
      sections.priceIncludes.push(line.replace(/^[-•✓✅]*/i, "").trim());
      continue;
    }

    // -----------------------------
    // CONTACT — phone, email, website
    // -----------------------------
    if (
      lower.includes("phone") ||
      lower.includes("tel") ||
      lower.includes("call me") ||
      lower.includes("email") ||
      lower.includes("website") ||
      lower.includes("www.")
    ) {
      sections.contact += line + " ";
      continue;
    }

    // -----------------------------
    // DEALER INFO — location, viewing, delivery, services, PX, cards, experience
    // -----------------------------
    if (
      lower.includes("open for sales") ||
      lower.includes("open for") ||
      lower.includes("forecourt") ||
      lower.includes("industrial") ||
      lower.includes("crowlas") ||
      lower.includes("fix auto") ||
      lower.includes("viewing") ||
      lower.includes("appointment") ||
      lower.includes("virtual viewing") ||
      lower.includes("old fashion") ||
      lower.includes("old fashioned") ||
      lower.includes("delivery") ||
      lower.includes("low loader") ||
      lower.includes("loan car") ||
      lower.includes("recovery") ||
      lower.includes("servicing repairs") ||
      lower.includes("servicing & repairs") ||
      lower.includes("repairs & mots") ||
      lower.includes("repairs & mot") ||
      lower.includes("servicing") ||
      lower.includes("repairs") ||
      lower.includes("mots") ||
      lower.includes("px poss") ||
      lower.includes("px possible") ||
      lower.includes("px") ||
      lower.includes("part exchange") ||
      lower.includes("cards") ||
      lower.includes("cards accepted") ||
      lower.includes("years motor trade") ||
      lower.includes("evenings") ||
      lower.includes("weekends")
    ) {
      sections.dealerInfo += line + " ";
      continue;
    }

    // -----------------------------
    // FEATURES — spec, packs, styling, tech, comfort, performance
    // -----------------------------
    if (
      lower.includes("face lift") ||
      lower.includes("facelift") ||
      lower.includes("start/stop") ||
      lower.includes("start stop") ||
      lower.includes("1.6dci") ||
      lower.includes("130bhp") ||
      lower.includes("6 speed") ||
      lower.includes("sports tourer") ||
      lower.includes("estate") ||
      lower.includes("gt line") ||
      lower.includes("metallic") ||
      lower.includes("styling") ||
      lower.includes("extras inc") ||
      lower.includes("extras include") ||
      lower.includes("factory") ||
      lower.includes("r-link") ||
      lower.includes("sat nav") ||
      lower.includes("nav") ||
      lower.includes("c/d stereo") ||
      lower.includes("aux port") ||
      lower.includes("remote controls") ||
      lower.includes("bluetooth") ||
      lower.includes("cruise") ||
      lower.includes("speed control") ||
      lower.includes("parking sensors") ||
      lower.includes("rear camera") ||
      lower.includes("day time running") ||
      lower.includes("daytime running") ||
      lower.includes("auto lights") ||
      lower.includes("auto wipers") ||
      lower.includes("digital climate") ||
      lower.includes("air con") ||
      lower.includes("alloys") ||
      lower.includes("isofix") ||
      lower.includes("multi air bags") ||
      lower.includes("multi airbags") ||
      lower.includes("seats") ||
      lower.includes("spoiler") ||
      lower.includes("wheels") ||
      lower.includes("exhaust") ||
      lower.includes("track line") ||
      lower.includes("aerodynamic") ||
      lower.includes("interior trim") ||
      lower.includes("mini connected") ||
      lower.includes("dab")
    ) {
      sections.features.push(line.replace(/^[-•✓✅]*/i, "").trim());
      continue;
    }

    // -----------------------------
    // MPG / TAX / VALUE — treat as intro (car pitch)
    // -----------------------------
    if (
      lower.includes("mpg") ||
      lower.includes("road tax") ||
      lower.includes("value")
    ) {
      sections.intro += line + " ";
      continue;
    }

    // -----------------------------
    // FALLBACK — anything else goes to intro
    // -----------------------------
    sections.intro += line + " ";
  }

  return sections;
}

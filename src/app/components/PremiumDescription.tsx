import React from "react";

interface Props {
  raw: string;
}

export function PremiumDescription({ raw }: Props) {
  if (!raw) return null;

  // Remove rogue ? but keep ticks ✓/✅
  const cleaned = raw.replace(/\?+/g, " ");

  // Split into lines
  const lines = cleaned
    .split(/\r?\n/)
    .map((line) => line.trim())
    .filter(Boolean);

  const elements: React.ReactNode[] = [];
  let currentList: string[] = [];

  const flushList = () => {
  if (currentList.length > 0) {
    elements.push(
      <ul className="list-none space-y-1 pl-0" key={elements.length}>
        {currentList.map((item, i) => (
          <li key={i} className="flex items-start gap-2">
            <span className="text-primary font-semibold">•</span>
            <span>{item.replace(/^✓|^✅/, "").trim()}</span>
          </li>
        ))}
      </ul>
    );
    currentList = [];
  }
};


  lines.forEach((line) => {
    if (line.startsWith("✓") || line.startsWith("✅")) {
      currentList.push(line);
    } else {
      flushList();
      elements.push(<p key={elements.length}>{line}</p>);
    }
  });

  flushList(); // flush any remaining ticks

return (
  <div className="text-gray-800 dark:text-white leading-[1.55] space-y-3 max-w-prose">
    {elements}
  </div>
);
}
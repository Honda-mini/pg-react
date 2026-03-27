type StatusBadgeProps = {
  featured?: number | boolean;
  reserved?: number | boolean;
  sold?: number | boolean;
};

const StatusBadge = ({ featured, reserved, sold }: StatusBadgeProps) => {
  const baseClasses =
    "absolute top-2 right-2 px-3 py-1 rounded text-white text-xs font-semibold shadow filter-none [filter:none]";

  if (sold) {
    return (
      <div className={`${baseClasses} bg-red-600`}>
        SOLD
      </div>
    );
  }

  if (reserved) {
    return (
      <div className={`${baseClasses} bg-amber-500`}>
        DEPOSIT TAKEN
      </div>
    );
  }

  if (featured) {
    return (
      <div className={`${baseClasses} bg-blue-600`}>
        POA
      </div>
    );
  }

  return null;
};

export default StatusBadge;

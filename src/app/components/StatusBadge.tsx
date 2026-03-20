type StatusBadgeProps = {
  featured?: number | boolean;
  reserved?: number | boolean;
  sold?: number | boolean;
};

const StatusBadge = ({ featured, reserved, sold }: StatusBadgeProps) => {
  if (sold) {
    return (
      <div className="absolute top-2 right-2 px-3 py-1 rounded bg-red-600 text-white text-xs font-semibold shadow">
        SOLD
      </div>
    );
  }

  if (reserved) {
    return (
      <div className="absolute top-2 right-2 px-3 py-1 rounded bg-amber-500 text-white text-xs font-semibold shadow">
        DEPOSIT TAKEN
      </div>
    );
  }

  if (featured) {
    return (
      <div className="absolute top-2 right-2 px-3 py-1 rounded bg-blue-600 text-white text-xs font-semibold shadow">
        POA
      </div>
    );
  }

  return null;
};

export default StatusBadge;

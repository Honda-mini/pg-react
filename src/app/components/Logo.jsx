export default function Logo({ variant = "full", className = "" }) {
  return (
    <div className={`flex items-center gap-3 ${className}`}>
{/* Badge */}
  {variant === "footer" ? (
    <div className="w-10 h-10 bg-blue-600 dark:bg-yellow-500 rounded-md flex items-center justify-center shadow-sm overflow-hidden">
      <span className="badge-font logo-large tracking-tightest font-black text-gray-900 logo-bold">
        PG
      </span>
    </div>
  ) : (
    <div className="w-10 h-10 bg-blue-600 dark:bg-yellow-500 rounded-md flex items-center justify-center shadow-sm overflow-hidden">
      <span className="badge-font logo-large tracking-tightest font-black text-white dark:text-gray-900 logo-bold">
        PG
      </span>
    </div>
  )}
  
  {variant === "footer" && (
  <span className="logo-font logo-large text-white">
    <span className="logo-bold">PG</span>
    <span className="logo-regular">Services</span>
  </span>
)}
      {variant === "full" && (
        <span className="logo-font logo-large text-gray-900 dark:text-white">
          <span className="logo-bold">PG</span>
          <span className="logo-regular">Services</span>
        </span>
      )}
    </div>
  );
}

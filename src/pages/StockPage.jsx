import { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { getThumbPaths } from "../utils/imagePaths";
import StatusBadge from "../app/components/StatusBadge";


export default function StockPage() {
  console.log("STOCK PAGE RENDERED");

const [cars, setCars] = useState([]);
const [loading, setLoading] = useState(true);

const [searchParams, setSearchParams] = useSearchParams();
const pageFromUrl = parseInt(searchParams.get("page")) || 1;
const [page, setPage] = useState(pageFromUrl);

useEffect(() => {
  setSearchParams({ page });
}, [page]);

const carsPerPage = 12;
const totalPages = Math.ceil(cars.length / carsPerPage);

const start = (page - 1) * carsPerPage;
const end = start + carsPerPage;

const paginatedCars = cars.slice(start, end);
useEffect(() => {
fetch(`${import.meta.env.VITE_API_URL}/getAllVehicles.php`)
    .then((res) => res.json())
    .then((data) => {
      const cleaned = data.map(car => ({
        ...car,
        sold: car.sold == 1,
        reserved: car.reserved == 1,
        featured: car.featured == 1
      }));
      setCars(cleaned);
      setLoading(false);
    })
    .catch(err => console.error("Fetch error:", err));
}, []);  if (loading) {
    return (
      <div className="flex justify-center items-center py-20 text-gray-800 dark:text-gray-300">
        Loading stock…
      </div>
    );
  }

  return (
    <div className="px-4 sm:px-6 lg:px-8 py-10 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
      <h1 className="text-3xl font-semibold mb-8 text-center">
        Our Current Stock
      </h1>

<div className="mt-10 mb-10 flex justify-center">
      <div className="flex items-center gap-2">

  {/* PREVIOUS */}
  <button
    disabled={page === 1}
    onClick={() => setPage(page - 1)}
    className="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 
               bg-white dark:bg-gray-800 
               disabled:opacity-40"
  >
    Prev
  </button>

  {/* PAGE NUMBERS */}
  {Array.from({ length: totalPages }, (_, i) => i + 1).map((num) => (
    <button
      key={num}
      onClick={() => setPage(num)}
      className={`
        px-3 py-2 rounded border 
        ${page === num 
          ? "bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 border-gray-900 dark:border-gray-100" 
          : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700"}
      `}
    >
      {num}
    </button>
  ))}

  {/* NEXT */}
  <button
    disabled={page === totalPages}
    onClick={() => setPage(page + 1)}
    className="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 
               bg-white dark:bg-gray-800 
               disabled:opacity-40"
  >
    Next
  </button>

</div>
</div>


      <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
  {paginatedCars.map((car) => {
  const images = car.images;

  const isSold = Number(car.sold) === 1;
  const isReserved = Number(car.reserved) === 1;
  const isDisabled = isSold || isReserved;

  return (
    <div
      key={car.stockID} className="relative">

  {/* BADGE ABOVE EVERYTHING */}
  <div className="absolute top-2 right-2 z-30 pointer-events-none">
    <StatusBadge
      featured={Number(car.featured)}
      reserved={isReserved ? 1 : 0}
      sold={isSold ? 1 : 0}
    />
  </div>

  {/* CARD WRAPPER (can safely be greyed out) */}
  <div
    className={`rounded-xl overflow-hidden shadow-lg transition-all ${
      isDisabled
        ? "opacity-60 grayscale cursor-not-allowed"
        : "hover:shadow-2xl"
    }`}
  >

    {/* FULL CARD CLICKABLE OVERLAY */}
    {!isDisabled && (
      <a
        href={`/vehicle/${car.stockID}`}
        className="absolute inset-0 z-20"
        aria-label={`View details for ${car.name}`}
      />
    )}

    {/* IMAGE */}
    <div className="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
      <img
        src={images.thumb400}
        srcSet={`${images.thumb400} 1x, ${images.thumb800} 2x`}
        alt={car.name}
        className="w-full h-full object-cover transition-transform group-hover:scale-105"
        loading="lazy"
      />
    </div>

    {/* CONTENT */}
    <div className="p-4">
      <h3 className="text-lg font-medium mb-1">{car.name}</h3>
      <p className="text-sm text-gray-600 dark:text-gray-300 mb-2">
        {car.mileage}
      </p>

      {/* PRICE / STATUS */}
      <div className="mb-4">
        {isSold ? (
          <p className="text-red-600 dark:text-red-400 font-semibold">Sold</p>
        ) : isReserved ? (
          <p className="text-amber-600 dark:text-amber-400 font-semibold">
            Deposit Taken
          </p>
        ) : Number(car.featured) === 1 ? (
          <p className="text-blue-600 dark:text-yellow-500 font-semibold">
            POA
          </p>
        ) : (
          <p className="text-lg font-semibold text-blue-600 dark:text-yellow-500">
            {car.price}
          </p>
        )}
      </div>

      {/* BUTTON */}
      <button
        disabled={isDisabled}
        className={`w-full py-2 rounded-lg transition-colors ${
          isDisabled
            ? "bg-gray-400 cursor-not-allowed"
            : "bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-yellow-400"
        }`}
      >
        View Details
      </button>
    </div>

  </div>
</div>

  );
})}

</div>


<div className="flex justify-center items-center gap-2 mt-10">

  {/* PREVIOUS */}
  <button
    disabled={page === 1}
    onClick={() => setPage(page - 1)}
    className="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 
               bg-white dark:bg-gray-800 
               disabled:opacity-40"
  >
    Prev
  </button>

  {/* PAGE NUMBERS */}
  {Array.from({ length: totalPages }, (_, i) => i + 1).map((num) => (
    <button
      key={num}
      onClick={() => setPage(num)}
      className={`
        px-3 py-2 rounded border 
        ${page === num 
          ? "bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900 border-gray-900 dark:border-gray-100" 
          : "bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700"}
      `}
    >
      {num}
    </button>
  ))}

  {/* NEXT */}
  <button
    disabled={page === totalPages}
    onClick={() => setPage(page + 1)}
    className="px-3 py-2 rounded border border-gray-300 dark:border-gray-700 
               bg-white dark:bg-gray-800 
               disabled:opacity-40"
  >
    Next
  </button>

</div>


    </div>
  );
}

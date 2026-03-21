import { useEffect, useState } from "react";
import { useSearchParams } from "react-router-dom";
import { getThumbPaths } from "../utils/imagePaths";


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
  fetch("http://localhost:8888/reactPg/api/getAllVehicles.php")
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

  return (
    <a key={car.stockID} href={`/vehicle/${car.stockID}`} className="group ...">
      <div className="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
        <img
          src={images.thumb400}
          srcSet={`${images.thumb400} 1x, ${images.thumb800} 2x`}
          alt={car.name}
          className="w-full h-full object-cover group-hover:scale-105 transition-transform"
          loading="lazy"
        />
      </div>
            <div className="p-4">
              <h3 className="text-lg font-medium mb-1">{car.name}</h3>

              <p className="text-sm text-gray-600 dark:text-gray-300 mb-2">
                {car.mileage}
              </p>
<div className="space-y3">
              {/* STATUS / PRICE */}
              {car.sold ? (
                <p className="inline-block px-3 py-1 text-xs font-semibold rounded bg-red-600 text-white">
                  Sold
                </p>
              ) : car.reserved ? (
                <p className="inline-block px-3 py-1 text-xs font-semibold rounded bg-yellow-500 text-black">
                  Deposit Taken
                </p>
              ) : car.featured ? (
                <p className="inline-block px-3 py-1 text-xs font-semibold rounded bg-blue-600 text-white">
                  POA
                </p>
              ) : (
                <p className="text-lg font-semibold text-blue-600 dark:text-yellow-500">
                  {car.price}
                </p>
              )}

<button
  disabled={car.sold === 1 || car.reserved === 1}
  className={`mt-4 w-full py-2 rounded-lg transition-colors ${
    car.sold === 1 || car.reserved === 1
      ? "bg-gray-400 cursor-not-allowed"
      : "bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-yellow-400"
  }`}
>
  View Details
</button> </div>
            </div>
          </a>
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

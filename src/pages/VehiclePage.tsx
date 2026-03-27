import React, { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import VehicleGalleryPremium from "../app/components/VehicleGalleryPremium";
import { PremiumDescription } from "../app/components/PremiumDescription";

const VehiclePage: React.FC = () => {
  const { id } = useParams();
  const [vehicle, setVehicle] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchVehicle = async () => {
      try {
        const res = await fetch(`${import.meta.env.VITE_API_URL}/car.php?id=${id}`);
        const data = await res.json();
        setVehicle(data.error ? null : data);
      } catch {
        setVehicle(null);
      } finally {
        setLoading(false);
      }
    };

    fetchVehicle();
  }, [id]);

  if (loading) return <p>Loading vehicle…</p>;
  if (!vehicle) return <p>Vehicle not found.</p>;

  const isSold = Number(vehicle.sold) === 1;
  const isReserved = Number(vehicle.reserved) === 1;
  const isDisabled = isSold || isReserved;

return (
  <>
    {/* Sticky Price Header (full width) */}
    <div className="sticky top-[60px] z-50 bg-white/85 dark:bg-gray-900/90 backdrop-blur-md shadow-sm py-4 w-full">
      <div className="max-w-5xl mx-auto flex items-center justify-center gap-6 px-4">
        <p className="text-3xl font-bold">£{vehicle.price.toLocaleString()}</p>

        {isDisabled ? (
          <button
            disabled
            className="bg-gray-400 text-white px-6 py-3 rounded-lg cursor-not-allowed"
          >
            Not Available
          </button>
        ) : (
          <Link
            to={`/contact?car=${encodeURIComponent(vehicle.name)}&id=${vehicle.stockID}`}
            className="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
          >
            Reserve Now
          </Link>
        )}
      </div>
    </div>

    {/* Main Page Container */}
    <div className="vehicle-page p-4 max-w-5xl mx-auto space-y-10">

      {/* Breadcrumbs */}
      <nav className="text-sm text-gray-500">
        <a href="/" className="hover:underline">Home</a> /
        <a href="/stock" className="hover:underline ml-1">Stock</a> /
        <span className="ml-1">{vehicle.name}</span>
      </nav>

      {/* Title */}
      <h1 className="text-3xl font-bold tracking-tight">{vehicle.name}</h1>

     

      {/* Gallery */}
      <VehicleGalleryPremium images={vehicle.images} />

      {/* Vehicle Details Card */}
      <div className="border border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-900 shadow-sm w-full">
        <h2 className="text-xl font-semibold mb-4 tracking-tight">Vehicle Details</h2>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-[15px]">

          {vehicle.yearPlate && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Year</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.yearPlate}</span>
            </div>
          )}

          {vehicle.mileage && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Mileage</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.mileage}</span>
            </div>
          )}

          {vehicle.fuelType && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Fuel</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.fuelType}</span>
            </div>
          )}

          {vehicle.transmission && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Transmission</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.transmission}</span>
            </div>
          )}

          {vehicle.engineSize && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Engine</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.engineSize}cc</span>
            </div>
          )}

          {vehicle.powerBhp && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">BHP</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.powerBhp}</span>
            </div>
          )}

          {vehicle.doorsNo && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Doors</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.doorsNo}</span>
            </div>
          )}

          {vehicle.bodyType && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Body</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.bodyType}</span>
            </div>
          )}

          {vehicle.colour && (
            <div className="flex items-center gap-2">
              <span className="text-gray-500 uppercase text-xs tracking-wide">Colour</span>
              <span className="font-medium">•</span>
              <span className="font-semibold">{vehicle.colour}</span>
            </div>
          )}

        </div>
      </div>

      {/* Description Card */}
      <div className="border border-gray-200 dark:border-gray-700 rounded-xl p-6 bg-white dark:bg-gray-900 shadow-sm w-full">
        <h2 className="text-xl font-semibold mb-4 tracking-tight">Description</h2>
        <PremiumDescription raw={vehicle.description} />
      </div>

      {/* Mobile Sticky CTA */}
      <div className="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t p-4 md:hidden">
        <div className="flex items-center gap-4">
          <span className="text-xl font-bold whitespace-nowrap">
            £{vehicle.price.toLocaleString()}
          </span>

          <div className="flex-1">
            {isDisabled ? (
              <button disabled className="w-full py-3 rounded-lg bg-gray-400 cursor-not-allowed">
                Not Available
              </button>
            ) : (
              <Link
                to={`/contact?car=${encodeURIComponent(vehicle.name)}&id=${vehicle.stockID}`}
                className="w-full py-3 rounded-lg bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-yellow-400 transition-colors block text-center"
              >
                Reserve Now
              </Link>
            )}
          </div>
        </div>
      </div>

    </div>
        
  </>


  );
};

export default VehiclePage;

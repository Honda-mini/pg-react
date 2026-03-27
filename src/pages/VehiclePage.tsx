import React, { useEffect, useState } from "react";
import { useParams } from "react-router-dom";
import VehicleGalleryPremium from "../app/components/VehicleGalleryPremium";
// import { Vehicle } from "../types/Vehicle"; // optional if you store interfaces separately
import { PremiumDescription } from "../app/components/PremiumDescription";
import { Link } from "react-router-dom";



const VehiclePage: React.FC = () => {
  const { id } = useParams();
  const [vehicle, setVehicle] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchVehicle = async () => {
      try {
        const res = await 
        fetch(`${import.meta.env.VITE_API_URL}/car.php?id=${id}`);
        const data = await res.json();

        if (data.error) {
          console.error("API Error:", data.error);
          setVehicle(null);
        } else {
          setVehicle(data);
        }
      } catch (error) {
        console.error("Fetch error:", error);
        setVehicle(null);
      } finally {
        setLoading(false);
      }
    };

    fetchVehicle()
  }, [id]);

  if (loading) return <p>Loading vehicle…</p>;
  if (!vehicle) return <p>Vehicle not found.</p>;

  const isSold = Number(vehicle.sold) === 1;
  const isReserved = Number(vehicle.reserved) === 1;
  const isDisabled = isSold || isReserved;

  return (
    
    <div className="vehicle-page p-4">

      <nav className="text-sm text-gray-500 mb-4">
  <a href="/" className="hover:underline">Home</a> /
  <a href="/stock" className="hover:underline ml-1">Stock</a> /
  <span className="ml-1">{vehicle.name}</span>
</nav>

      <h1>{vehicle.name}</h1>

<div className="sticky top-[60px] z-50 bg-white/85 dark:bg-gray-900/90 backdrop-blur-md rounded-lg">
  <div className="flex items-center justify-center gap-4 mt-4">
    <p className="text-3xl font-bold">
      £{vehicle.price.toLocaleString()}
    </p>

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


      <VehicleGalleryPremium images={vehicle.images} />

<div className="spec-grid mt-6 grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
  {vehicle.yearPlate && <div><strong>Year:</strong> {vehicle.yearPlate}</div>}
  {vehicle.mileage && <div><strong>Mileage:</strong> {vehicle.mileage}</div>}
  {vehicle.fuelType && <div><strong>Fuel:</strong> {vehicle.fuelType}</div>}
  {vehicle.transmission && <div><strong>Transmission:</strong> {vehicle.transmission}</div>}
  {vehicle.engineSize && <div><strong>Engine:</strong> {vehicle.engineSize}cc</div>}
  {vehicle.powerBhp && <div><strong>BHP:</strong> {vehicle.powerBhp}</div>}
  {vehicle.doorsNo && <div><strong>Doors:</strong> {vehicle.doorsNo}</div>}
  {vehicle.bodyType && <div><strong>Body:</strong> {vehicle.bodyType}</div>}
  {vehicle.colour && <div><strong>Colour:</strong> {vehicle.colour}</div>}
</div>

      <div className="vehicle-description">
<PremiumDescription raw={vehicle.description} />
      </div>
      <div className="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t p-4 md:hidden">
  <div className="flex items-center gap-4">
    
    <span className="text-xl font-bold whitespace-nowrap">
      £{vehicle.price.toLocaleString()}
    </span>

    <div className="flex-1">
      {isDisabled ? (
        <button
          disabled
          className="w-full py-3 rounded-lg bg-gray-400 cursor-not-allowed"
        >
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
    
  );
};

export default VehiclePage;

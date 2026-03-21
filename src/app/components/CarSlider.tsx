import { useEffect, useState } from "react";
import Slider from "react-slick";
import { ChevronLeft, ChevronRight } from "lucide-react";
import StatusBadge from "./StatusBadge";


interface Car {
  id: number;
  name: string;
  price: number;
  year: number;
  mileage: string;
  image: string;
  featured: number;
  reserved: number;
  sold: number;
}

function NextArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white/90 dark:bg-gray-800/90 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 hover:text-white transition-all group shadow-lg"
      aria-label="Next slide"
    >
      <ChevronRight className="w-6 h-6 text-gray-900 dark:text-white group-hover:text-white dark:group-hover:text-gray-900" />
    </button>
  );
}

function PrevArrow(props: any) {
  const { onClick } = props;
  return (
    <button
      onClick={onClick}
      className="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-12 h-12 bg-white/90 dark:bg-gray-800/90 rounded-full flex items-center justify-center hover:bg-blue-600 dark:hover:bg-yellow-500 hover:text-white transition-all group shadow-lg"
      aria-label="Previous slide"
    >
      <ChevronLeft className="w-6 h-6 text-gray-900 dark:text-white group-hover:text-white dark:group-hover:text-gray-900" />
    </button>
  );
}

export function CarSlider() {
  const [cars, setCars] = useState<Car[]>([]);

  useEffect(() => {
    fetch("http://localhost:8888/reactPg/api/cars.php")
      .then((res) => res.json())
      .then((data) => {
        const formatted = data.map((car: any) => ({
          id: Number(car.stockID),
          name: car.name,
          price: car.price,
          year: car.yearPlate,
          mileage: car.mileage,
          image: car.image,
          featured: Number(car.featured),
          reserved: Number(car.reserved),
          sold: Number(car.sold),
        }));
        setCars(formatted);
      });
  }, []);

  const settings = {
    dots: false,
    infinite: cars.length > 1,
    speed: 500,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 4000,
    pauseOnHover: true,
    nextArrow: <NextArrow />,
    prevArrow: <PrevArrow />,
   // rtl: true,
    responsive: [
      { breakpoint: 769, settings: { slidesToShow: 2 }},
      { breakpoint: 480, settings: { slidesToShow: 1 }},
    ],
  };

  return (
    <section className="py-20 bg-gray-50 dark:bg-gray-800" id="inventory">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div className="text-center mb-12">
          <h2 className="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            Featured Vehicles
          </h2>
          <p className="text-lg text-gray-600 dark:text-gray-300">
            Explore our handpicked selection of premium automobiles
          </p>
        </div>

        <Slider {...settings}>
          {cars.map((car) => (
            <div key={car.id} className="px-4 min-h-[584px]">
          <div className="bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-shadow h-full flex flex-col">
<div className="relative h-64 md:h-72 lg:h-80 overflow-hidden">

  {/* Badge ABOVE everything, its own layer */}
  <div className="absolute top-2 right-2 z-30 pointer-events-none">
    <StatusBadge
      featured={car.featured}
      reserved={car.reserved}
      sold={car.sold}
    />
  </div>

  {/* Image BELOW the badge */}
  <img
    src={car.image}
    alt={car.name}
    className={`w-full h-full object-cover hover:scale-110 transition-transform duration-300 ${
      car.sold === 1 || car.reserved === 1 ? "grayscale opacity-60" : ""
    }`}
    onError={(e) => (e.currentTarget.src = "/images/no-image.svg")}
  />

</div>

                <div className="p-6 flex-1 flex flex-col justify-between">
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-3">
                      {car.name}
                    </h3>

                    <div className="flex items-center justify-between mb-4">
                      <span className="text-sm text-gray-600 dark:text-gray-400">
                        {car.year} • {car.mileage}
                      </span>
                    </div>
                  </div>

                  <div className="space-y-3">
<div className="text-xl font-bold text-blue-600 dark:text-yellow-500">
  {car.sold === 1 ? (
    <span className="text-red-600 dark:text-red-400">Sold</span>
  ) : car.reserved === 1 ? (
    <span className="text-amber-600 dark:text-amber-400">Deposit Taken</span>
  ) : car.featured === 1 ? (
    <span className="text-blue-600 dark:text-yellow-500">POA</span>
  ) : (
    car.price
  )}
</div>

<button
  disabled={car.sold === 1 || car.reserved === 1}
  className={`w-full py-3 rounded-lg transition-colors ${
    car.sold === 1 || car.reserved === 1
      ? "bg-gray-400 cursor-not-allowed"
      : "bg-blue-600 dark:bg-yellow-500 text-white dark:text-gray-900 hover:bg-blue-700 dark:hover:bg-yellow-400"
  }`}
>
  View Details
</button>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </Slider>
      </div>
    </section>
  );
}

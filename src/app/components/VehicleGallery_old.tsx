import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";

interface VehicleGalleryProps {
  images: string[];
}

const VehicleGallery: React.FC<VehicleGalleryProps> = ({ images }) => {
  if (!images || images.length === 0) {
    return <p>No images available.</p>;
  }

  return (
    <div className="vehicle-gallery">
      {/* Mobile portrait carousel */}
      <div className="block md:hidden">
        <Swiper spaceBetween={10} slidesPerView={1}>
          {images.map((src, index) => (
            <SwiperSlide key={index}>
              <img src={src} className="w-full h-auto object-cover" />
            </SwiperSlide>
          ))}
        </Swiper>
      </div>

      {/* Desktop grid */}
      <div className="hidden md:grid grid-cols-3 p-4 gap-4">
        {images.map((src, index) => (
          <img key={index} src={src} className="w-full rounded-lg" />
        ))}
      </div>
    </div>
  );
};

export default VehicleGallery;

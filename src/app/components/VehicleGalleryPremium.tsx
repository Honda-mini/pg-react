import { useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";

import Lightbox from "yet-another-react-lightbox";
import Zoom from "yet-another-react-lightbox/plugins/zoom";
import Fullscreen from "yet-another-react-lightbox/plugins/fullscreen";
import Thumbnails from "yet-another-react-lightbox/plugins/thumbnails";

import "yet-another-react-lightbox/styles.css";
import "yet-another-react-lightbox/plugins/thumbnails.css";

interface Props {
  images: string[];
}

export default function VehicleGalleryPremium({ images }: Props) {
  const [active, setActive] = useState(0);
  const [lightboxOpen, setLightboxOpen] = useState(false);

  if (!images || images.length === 0) {
    return <p>No images available.</p>;
  }

  return (
    <div className="vehicle-gallery-premium w-full">

      {/* MOBILE HERO SWIPER */}
      <div className="block md:hidden">
        <Swiper
          spaceBetween={10}
          slidesPerView={1}
          onSlideChange={(swiper) => setActive(swiper.activeIndex)}
        >
          {images.map((src, i) => (
            <SwiperSlide key={i}>
              <img
                src={src}
                className="w-full h-[70vh] object-cover"
                alt={`Vehicle image ${i + 1}`}
              />
            </SwiperSlide>
          ))}
        </Swiper>
      </div>

      {/* DESKTOP HERO + THUMBNAILS */}
      <div className="hidden md:flex gap-4">

        {/* HERO IMAGE */}
        <div
          className="flex-1 flex mt-2.5 items-center justify-center rounded-lg cursor-pointer"
        >
          <img
            src={images[active]}
            className="max-h-[650px] h-auto object-contain rounded-lg shadow-lg transition-all"
            alt="Main vehicle"
                      onClick={() => setLightboxOpen(true)}

          />
        </div>

        {/* THUMBNAILS */}
        <div className="w-32 flex flex-col gap-3 overflow-y-scroll max-h-[650px] pr-2">
          {images.map((src, i) => (
            <button
              key={i}
              onClick={() => setActive(i)}
              className={`border-2 rounded-lg overflow-hidden transition-all flex-shrink-0 ${
                active === i
                  ? "border-blue-600 dark:border-yellow-500"
                  : "border-transparent"
              }`}
            >
<img
  src={src.replace(/\/([^\/]+)$/, "/$1")}
  className="w-full h-24 object-cover"
  alt={`Thumbnail ${i + 1}`}
/>

            </button>
          ))}
        </div>
      </div>

      {/* LIGHTBOX (DESKTOP ONLY) */}
      {lightboxOpen && (
        <Lightbox
          open={lightboxOpen}
          close={() => setLightboxOpen(false)}
          slides={images.map((src) => ({ src }))}
          index={active}
          plugins={[Zoom, Fullscreen, Thumbnails]}
          on={{
            view: ({ index }) => setActive(index),
          }}
        />
      )}
    </div>
  );
}

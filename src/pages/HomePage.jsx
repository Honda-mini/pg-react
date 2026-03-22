import { Hero } from '../app/components/Hero';
import { CarSlider } from '../app/components/CarSlider';
import { InfoSection } from '../app/components/InfoSection';
import { useEffect } from 'react';

export default function HomePage() {
  useEffect(() => {
    if (window.location.hash) {
      const el = document.querySelector(window.location.hash);
      if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
      }
    }
  }, []);

  return (
    <>
      <Hero />
      <CarSlider />
      <InfoSection />
    </>
  );
}

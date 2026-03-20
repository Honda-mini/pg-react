// src/utils/imagePaths.js

/**
 * Returns the correct 400px + 800px thumbnail paths
 * for a given car and image number.
 */
export function getThumbPaths(carId, imageId) {
  return {
    src: `/images/cars/${carId}/${imageId}_400.webp`,
    srcSet: `/images/cars/${carId}/${imageId}_400.webp 1x, 
             /images/cars/${carId}/${imageId}_800.webp 2x`
  };
}

/**
 * Returns the correct full-size (1920px) image path.
 */
export function getFullImagePath(carId, imageId) {
  return `/images/cars/${carId}/${imageId}.webp`;
}

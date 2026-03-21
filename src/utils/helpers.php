<?php

// ---------------------------------------------
// IMAGE HELPERS
// ---------------------------------------------

function getMainImage($stockID) {
    $base = "img/cars/$stockID/";
    $orderFile = $base . 'order.json';

    // 1. Check order.json
    if (file_exists($orderFile)) {
        $ordered = json_decode(file_get_contents($orderFile), true);
        if (is_array($ordered)) {
            foreach ($ordered as $img) {
                if (is_file($base . $img) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $img)) {
                    return $base . $img;
                }
            }
        }
    }

    // 2. Fallback to 1.jpg / 1.jpeg
    if (file_exists($base . '1.jpg')) return $base . '1.jpg';
    if (file_exists($base . '1.jpeg')) return $base . '1.jpeg';

    // 3. No image
    return null;
}

function getAllImages($stockID) {
    $base = "img/cars/$stockID/";
    $images = [];

    // 1. Use order.json if available
    $orderFile = $base . 'order.json';
    if (file_exists($orderFile)) {
        $ordered = json_decode(file_get_contents($orderFile), true);
        if (is_array($ordered)) {
            foreach ($ordered as $img) {
                if (is_file($base . $img)) {
                    $images[] = $base . $img;
                }
            }
        }
    }

    // 2. Fallback: any image files
    if (empty($images)) {
        foreach (glob($base . "*.{jpg,jpeg,png,gif}", GLOB_BRACE) as $file) {
            $images[] = $file;
        }
    }

    return $images;
}


// ---------------------------------------------
// FORMATTING HELPERS
// ---------------------------------------------

function formatMileage($mileage) {
    if (!$mileage) return null;
    $m = preg_replace('/[^0-9]/', '', $mileage);
    if ($m === '') return null;

    if ($m >= 1000) {
        return number_format($m / 1000, 1) . "k miles";
    }

    return number_format($m) . " miles";
}

function formatEngineSize($cc) {
    if (!$cc) return null;
    return number_format($cc / 1000, 1) . "L";
}

function formatPrice($price) {
    if (!$price) return null;
    return "£" . number_format((int)preg_replace('/[^0-9]/', '', $price));
}


// ---------------------------------------------
// ICON MAPPING (adjust to your SVG filenames)
// ---------------------------------------------

function getFuelIcon($fuel) {
    $fuel = strtolower($fuel);
    return match ($fuel) {
        "petrol" => "icons/petrol.svg",
        "diesel" => "icons/diesel.svg",
        "electric" => "icons/electric.svg",
        "hybrid" => "icons/hybrid.svg",
        default => "icons/fuel.svg"
    };
}

function getGearboxIcon($gearbox) {
    $gearbox = strtolower($gearbox);
    return match ($gearbox) {
        "manual" => "icons/manual.svg",
        "automatic", "auto" => "icons/auto.svg",
        default => "icons/gearbox.svg"
    };
}

function getBodyIcon($body) {
    $body = strtolower($body);
    return match ($body) {
        "hatchback" => "icons/hatch.svg",
        "saloon" => "icons/saloon.svg",
        "suv" => "icons/suv.svg",
        "estate" => "icons/estate.svg",
        default => "icons/car.svg"
    };
}


// ---------------------------------------------
// SPEC ROW BUILDER (for stock cards + vehicle page)
// ---------------------------------------------

function buildSpecRow($car) {
    $specs = [];

    if (!empty($car['engineSize'])) {
        $specs[] = formatEngineSize($car['engineSize']);
    }

    if (!empty($car['fuelType'])) {
        $specs[] = ucfirst($car['fuelType']);
    }

    if (!empty($car['transmission'])) {
        $specs[] = ucfirst($car['transmission']);
    }

    if (!empty($car['mileage'])) {
        $specs[] = formatMileage($car['mileage']);
    }

    return implode(" • ", $specs);
}

?>

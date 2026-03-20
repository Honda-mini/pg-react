import fs from 'fs';
import path from 'path';
import sharp from 'sharp';

// SOURCE: original 5712px JPGs
const originalsRoot = '../pg/images/cars';

// DESTINATION: reactPg public folder
const outputRoot = './public/images/cars';

async function generate800px() {
  const carFolders = fs.readdirSync(originalsRoot);

  for (const folder of carFolders) {
    const sourceCarPath = path.join(originalsRoot, folder);
    const destCarPath = path.join(outputRoot, folder);

    // Skip non-folders
    if (!fs.lstatSync(sourceCarPath).isDirectory()) continue;

    // Ensure destination folder exists
    if (!fs.existsSync(destCarPath)) {
      fs.mkdirSync(destCarPath, { recursive: true });
      console.log(`Created folder: ${destCarPath}`);
    }

    const files = fs.readdirSync(sourceCarPath);

    for (const file of files) {
      const ext = path.extname(file).toLowerCase();
      const base = path.basename(file, ext);

      // Only process original JPGs
      if (ext !== '.jpg' && ext !== '.jpeg') continue;

      const inputPath = path.join(sourceCarPath, file);
      const outputPath = path.join(destCarPath, `${base}_800.webp`);

      // Skip if already exists
      if (fs.existsSync(outputPath)) {
        console.log(`Skipping existing: ${folder}/${base}_800.webp`);
        continue;
      }

      try {
        await sharp(inputPath)
          .resize({ width: 800 })
          .webp({ quality: 85 })
          .toFile(outputPath);

        console.log(`Created: ${folder}/${base}_800.webp`);
      } catch (err) {
        console.error(`Error processing ${folder}/${file}:`, err);
      }
    }
  }

  console.log('800px generation complete.');
}

generate800px();


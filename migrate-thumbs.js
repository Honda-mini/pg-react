import fs from 'fs';
import path from 'path';

const carsRoot = './images/cars';

function migrateThumbs() {
  const carFolders = fs.readdirSync(carsRoot);

  carFolders.forEach(folder => {
    const carPath = path.join(carsRoot, folder);
    const thumbsPath = path.join(carPath, 'thumbs');
    const fullsizePath = path.join(carPath, 'fullsize');

    //
    // 1. DELETE FULL-SIZE JPGs
    //
    if (fs.existsSync(carPath)) {
      const fullFiles = fs.readdirSync(carPath);

      fullFiles.forEach(file => {
        const ext = path.extname(file).toLowerCase();
        if (ext === '.jpg' || ext === '.jpeg') {
          fs.unlinkSync(path.join(carPath, file));
          console.log(`Deleted FULL JPG: ${folder}/${file}`);
        }
      });
    }

    //
    // 2. PROCESS THUMBS FOLDER
    //
    if (!fs.existsSync(thumbsPath)) {
      console.log(`No thumbs folder in ${folder}, skipping`);
      return;
    }

    const thumbFiles = fs.readdirSync(thumbsPath);

    thumbFiles.forEach(file => {
      const ext = path.extname(file).toLowerCase();
      const base = path.basename(file, ext);

      // Delete JPG thumbs
      if (ext === '.jpg' || ext === '.jpeg') {
        fs.unlinkSync(path.join(thumbsPath, file));
        console.log(`Deleted THUMB JPG: ${folder}/${file}`);
        return;
      }

      // Move WebP thumbs → {id}_400.webp
      if (ext === '.webp') {
        const newName = `${base}_400.webp`;
        const oldPath = path.join(thumbsPath, file);
        const newPath = path.join(carPath, newName);

        fs.renameSync(oldPath, newPath);
        console.log(`Moved THUMB: ${folder}/${file} → ${newName}`);
      }
    });

    //
    // 3. REMOVE THUMBS FOLDER IF EMPTY
    //
    const remaining = fs.readdirSync(thumbsPath);
    if (remaining.length === 0) {
      fs.rmdirSync(thumbsPath);
      console.log(`Removed empty thumbs folder in ${folder}`);
    }
  });

  console.log('Migration complete.');
}

migrateThumbs();

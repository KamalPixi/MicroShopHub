const fs = require('fs');
const path = require('path');

const srcDir = path.resolve(__dirname, '../out');
const destDir = path.resolve(__dirname, '../../public');
const bladeViewFile = path.resolve(__dirname, '../../resources/views/storefront.blade.php');
const manifestFile = path.resolve(destDir, '.next-build-manifest.json');

// Helper to recursively delete files and empty folders
function deleteRecursive(itemPath) {
  if (fs.existsSync(itemPath)) {
    const stats = fs.statSync(itemPath);
    if (stats.isDirectory()) {
      fs.readdirSync(itemPath).forEach((child) => {
        deleteRecursive(path.join(itemPath, child));
      });
      fs.rmdirSync(itemPath);
    } else {
      fs.unlinkSync(itemPath);
    }
  }
}

// Helper to recursively copy static assets (ignoring HTML, TXT, and __next internal files/directories)
function copyRecursive(src, dest, copiedPaths = []) {
  const stats = fs.statSync(src);
  const name = path.basename(src);

  if (stats.isDirectory()) {
    // Ignore route-specific static HTML directories and Next.js internals
    const ignoredDirs = ['cart', 'checkout', 'flash-sale', 'product', 'search', '404', '_not-found'];
    if ((ignoredDirs.includes(name) || name.startsWith('__next')) && path.dirname(src) === srcDir) {
      return;
    }

    if (!fs.existsSync(dest)) {
      fs.mkdirSync(dest, { recursive: true });
    }
    fs.readdirSync(src).forEach((child) => {
      copyRecursive(path.join(src, child), path.join(dest, child), copiedPaths);
    });
  } else {
    // Ignore all HTML files, TXT files, and __next internal files from copying into public folder
    if (name.endsWith('.html') || name.endsWith('.txt') || name.startsWith('__next')) {
      return;
    }

    // Ensure parent directory exists
    const parentDir = path.dirname(dest);
    if (!fs.existsSync(parentDir)) {
      fs.mkdirSync(parentDir, { recursive: true });
    }
    fs.copyFileSync(src, dest);
    // Track relative path for manifest
    const relPath = path.relative(destDir, dest);
    copiedPaths.push(relPath);
  }
}

function deploy() {
  console.log('--- Next.js Static Export Deployment Script ---');
  console.log(`Source: ${srcDir}`);
  console.log(`Destination (Public): ${destDir}`);
  console.log(`Blade Template Destination: ${bladeViewFile}`);

  if (!fs.existsSync(srcDir)) {
    console.error(`Error: Source directory "${srcDir}" does not exist. Please run "next build" first.`);
    process.exit(1);
  }

  // 1. Clean up files from previous deployment if manifest exists
  if (fs.existsSync(manifestFile)) {
    try {
      console.log('Reading previous deployment manifest...');
      const previousFiles = JSON.parse(fs.readFileSync(manifestFile, 'utf8'));
      if (Array.isArray(previousFiles)) {
        console.log(`Cleaning ${previousFiles.length} files from previous deployment...`);
        previousFiles.forEach((file) => {
          const filePath = path.join(destDir, file);
          if (fs.existsSync(filePath)) {
            const stats = fs.statSync(filePath);
            if (!stats.isDirectory()) {
              fs.unlinkSync(filePath);
            }
          }
        });

        // Clean up empty folders created by the previous deploy
        const dirs = [...new Set(previousFiles.map(f => path.dirname(f)))]
          .filter(d => d !== '.')
          .sort((a, b) => b.length - a.length); // Deepest first

        dirs.forEach((dir) => {
          const dirPath = path.join(destDir, dir);
          if (fs.existsSync(dirPath)) {
            try {
              if (fs.readdirSync(dirPath).length === 0) {
                fs.rmdirSync(dirPath);
              }
            } catch (dirErr) {
              // Ignore directory removal errors
            }
          }
        });
      }
    } catch (err) {
      console.warn('Warning: Failed to clean up previous build. Manifest might be corrupted:', err.message);
    }
  }

  // Also clean up any legacy HTML directories that might be left in public
  const legacyDirs = ['cart', 'checkout', 'flash-sale', 'product', 'search', '404', '_not-found'];
  legacyDirs.forEach((dir) => {
    const dirPath = path.join(destDir, dir);
    if (fs.existsSync(dirPath)) {
      console.log(`Cleaning legacy static HTML route directory: public/${dir}`);
      deleteRecursive(dirPath);
    }
  });
  const legacyFiles = ['index.html', '404.html', 'index.txt'];
  legacyFiles.forEach((file) => {
    const filePath = path.join(destDir, file);
    if (fs.existsSync(filePath)) {
      console.log(`Cleaning legacy static file: public/${file}`);
      fs.unlinkSync(filePath);
    }
  });

  // Clean up any files starting with __next in the public root
  if (fs.existsSync(destDir)) {
    fs.readdirSync(destDir).forEach((file) => {
      if (file.startsWith('__next')) {
        const filePath = path.join(destDir, file);
        console.log(`Cleaning legacy Next.js internal file: public/${file}`);
        fs.unlinkSync(filePath);
      }
    });
  }

  // 2. Copy new static assets
  console.log('Copying static assets (excluding HTML) from Next.js export to Laravel public directory...');
  const newCopiedPaths = [];
  copyRecursive(srcDir, destDir, newCopiedPaths);

  // 3. Write storefront Blade template from index.html
  const nextIndexPath = path.join(srcDir, 'index.html');
  if (fs.existsSync(nextIndexPath)) {
    console.log('Converting Next.js index.html entry shell into storefront.blade.php...');
    let htmlContent = fs.readFileSync(nextIndexPath, 'utf8');
    
    // Ensure parent view directory exists
    const viewParentDir = path.dirname(bladeViewFile);
    if (!fs.existsSync(viewParentDir)) {
      fs.mkdirSync(viewParentDir, { recursive: true });
    }
    
    // Write view file
    fs.writeFileSync(bladeViewFile, htmlContent, 'utf8');
    console.log(`Blade template successfully generated at: ${bladeViewFile}`);
  } else {
    console.error('Error: index.html not found in compiled Next.js export.');
  }

  // 4. Write new manifest file (excluding the manifest file itself if copied, which it won't be)
  fs.writeFileSync(manifestFile, JSON.stringify(newCopiedPaths, null, 2), 'utf8');
  console.log(`Deployment successful! Copied ${newCopiedPaths.length} static assets.`);
  console.log(`Manifest written to: ${manifestFile}`);
  console.log('------------------------------------------------');
}

deploy();

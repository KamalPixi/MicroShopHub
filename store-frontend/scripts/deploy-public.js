const fs = require('fs');
const path = require('path');

const srcDir = path.resolve(__dirname, '../out');
const destDir = path.resolve(__dirname, '../../public');
const bladeViewFile = path.resolve(__dirname, '../../resources/views/storefront.blade.php');
const manifestFile = path.resolve(destDir, '.next-build-manifest.json');

// SPA route directories that must NEVER exist in public/
// (if they do, Apache/nginx will serve them as directories and bypass Laravel)
const SPA_ROUTE_DIRS = [
  'dashboard', 'cart', 'checkout', 'flash-sale', 'product',
  'search', '404', '_not-found', 'login', 'register',
  'about', 'faq', 'contact', 'privacy-policy', 'terms',
  'refund-policy', 'shipping', 'cookie-policy', 'spa',
];

function deleteRecursive(itemPath) {
  if (!fs.existsSync(itemPath)) return;
  if (fs.statSync(itemPath).isDirectory()) {
    fs.readdirSync(itemPath).forEach((c) => deleteRecursive(path.join(itemPath, c)));
    fs.rmdirSync(itemPath);
  } else {
    fs.unlinkSync(itemPath);
  }
}

// Copy ONLY static assets (_next/, images, fonts etc.) — never HTML/TXT/route dirs
function copyStaticAssets(src, dest, copiedPaths = []) {
  const stats = fs.statSync(src);
  const name = path.basename(src);

  if (stats.isDirectory()) {
    // Skip route-level directories entirely — we only want _next/ and other asset dirs
    if (path.dirname(src) === srcDir && name !== '_next') return;

    if (!fs.existsSync(dest)) fs.mkdirSync(dest, { recursive: true });
    fs.readdirSync(src).forEach((child) =>
      copyStaticAssets(path.join(src, child), path.join(dest, child), copiedPaths)
    );
  } else {
    // Never copy html/txt files into public
    if (name.endsWith('.html') || name.endsWith('.txt')) return;

    const parentDir = path.dirname(dest);
    if (!fs.existsSync(parentDir)) fs.mkdirSync(parentDir, { recursive: true });
    fs.copyFileSync(src, dest);
    copiedPaths.push(path.relative(destDir, dest));
  }
}

function deploy() {
  console.log('--- Next.js Static Export Deployment ---');

  if (!fs.existsSync(srcDir)) {
    console.error(`Error: "${srcDir}" not found. Run "next build" first.`);
    process.exit(1);
  }

  // 1. Clean files from previous deployment
  if (fs.existsSync(manifestFile)) {
    try {
      const prev = JSON.parse(fs.readFileSync(manifestFile, 'utf8'));
      if (Array.isArray(prev)) {
        prev.forEach((f) => {
          const fp = path.join(destDir, f);
          if (fs.existsSync(fp) && !fs.statSync(fp).isDirectory()) fs.unlinkSync(fp);
        });
        console.log(`Cleaned ${prev.length} files from previous build.`);
      }
    } catch (e) {
      console.warn('Warning: could not read previous manifest:', e.message);
    }
  }

  // 2. Remove any SPA route directories from public/ so .htaccess can route to index.php
  SPA_ROUTE_DIRS.forEach((dir) => {
    const dirPath = path.join(destDir, dir);
    if (fs.existsSync(dirPath)) {
      console.log(`Removing public/${dir}/ (would block Laravel routing)`);
      deleteRecursive(dirPath);
    }
  });

  // 3. Copy _next/ static assets only
  console.log('Copying _next/ static assets to public/...');
  const copiedPaths = [];
  copyStaticAssets(srcDir, destDir, copiedPaths);
  console.log(`Copied ${copiedPaths.length} static asset files.`);

  // 4. Use index.html as the single SPA shell (storefront.blade.php)
  const indexHtml = path.join(srcDir, 'index.html');
  if (!fs.existsSync(indexHtml)) {
    console.error('Error: index.html not found in Next.js export output.');
    process.exit(1);
  }
  const html = fs.readFileSync(indexHtml, 'utf8');
  const viewDir = path.dirname(bladeViewFile);
  if (!fs.existsSync(viewDir)) fs.mkdirSync(viewDir, { recursive: true });
  fs.writeFileSync(bladeViewFile, html, 'utf8');
  console.log('storefront.blade.php updated from index.html (SPA shell).');

  // 5. Write manifest
  fs.writeFileSync(manifestFile, JSON.stringify(copiedPaths, null, 2), 'utf8');
  console.log('Deployment complete!');
  console.log('--------------------------------------');
}

deploy();

const esbuild = require("esbuild");
const fs = require("fs").promises;
const path = require("path");

async function copyFolder(src, dest) {
  await fs.mkdir(dest, { recursive: true });
  const entries = await fs.readdir(src, { withFileTypes: true });
  for (let entry of entries) {
    const srcPath = path.join(src, entry.name);
    const destPath = path.join(dest, entry.name);
    if (entry.isDirectory()) {
      await copyFolder(srcPath, destPath);
    } else {
      await fs.copyFile(srcPath, destPath);
    }
  }
}

const buildJS = esbuild.build({
  entryPoints: ["resources/pub/_js/*.js"],
  bundle: true,
  minify: true,
  outdir: "public/_dist/js",
  treeShaking: true,
  format: "esm",
});

const buildCSS = esbuild.build({
  entryPoints: ["resources/pub/_css/*.css"],
  bundle: true,
  minify: true,
  outdir: "public/_dist/css",
  loader: { ".css": "css", ".webp": "file" },
});

async function copyAssets() {
  await copyFolder("resources/pub/_assets", "public/_assets");
}

Promise.all([buildJS, buildCSS])
  .then(copyAssets)
  .catch((err) => {
    console.error(err);
    process.exit(1);
  });

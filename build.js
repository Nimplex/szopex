const esbuild = require("esbuild");
const fs = require("fs/promises");
const fsSync = require("fs");
const path = require("path");

const args = process.argv.slice(2);
const mode =
  args.includes("--prod") || args.includes("-p") ? "production" : "development";
const watch = args.includes("--watch") || args.includes("-w");
const isProd = mode === "production";

console.log(
  `\nBuilding in ${mode} mode${watch ? " (watch enabled)" : ""}...\n`,
);

async function copyFolder(src, dest) {
  try {
    await fs.mkdir(dest, { recursive: true });
    const entries = await fs.readdir(src, { withFileTypes: true });

    for (const entry of entries) {
      const srcPath = path.join(src, entry.name);
      const destPath = path.join(dest, entry.name);

      if (entry.isDirectory()) {
        await copyFolder(srcPath, destPath);
      } else {
        await fs.copyFile(srcPath, destPath);
      }
    }
  } catch (err) {
    console.error(`Error copying ${src}:`, err.message);
    throw err;
  }
}

const commonOptions = {
  bundle: true,
  treeShaking: true,
  format: "esm",
  logLevel: "info",
};

const jsOptions = {
  ...commonOptions,
  entryPoints: ["resources/pub/_js/*.js"],
  outdir: "public/_dist/js",
  minify: isProd,
  sourcemap: !isProd,
  target: isProd ? ["es2020"] : ["esnext"],
};

const cssOptions = {
  ...commonOptions,
  entryPoints: ["resources/pub/_css/*.css"],
  outdir: "public/_dist/css",
  loader: {
    ".css": "css",
    ".webp": "file",
    ".png": "file",
    ".jpg": "file",
    ".jpeg": "file",
    ".svg": "file",
  },
  minify: isProd,
  sourcemap: !isProd,
};

async function build() {
  try {
    const startTime = Date.now();

    await Promise.all([esbuild.build(jsOptions), esbuild.build(cssOptions)]);

    await copyFolder("resources/pub/_assets", "public/_assets");

    const duration = Date.now() - startTime;
    console.log(`\nBuild completed in ${duration}ms\n`);
  } catch (err) {
    console.error("\nBuild failed:", err);
    process.exit(1);
  }
}

async function startWatch() {
  try {
    console.log("Starting watch mode...\n");

    const jsContext = await esbuild.context(jsOptions);
    const cssContext = await esbuild.context(cssOptions);

    await Promise.all([jsContext.watch(), cssContext.watch()]);

    const assetsWatcher = fsSync.watch(
      "resources/pub/_assets",
      { recursive: true },
      async (eventType, filename) => {
        if (filename) {
          console.log(`Asset changed: ${filename}`);
          try {
            await copyFolder("resources/pub/_assets", "public/_assets");
            console.log("Assets copied\n");
          } catch (err) {
            console.error("Error copying assets:", err.message);
          }
        }
      },
    );

    console.log("Watch mode active. Press Ctrl+C to stop.\n");

    process.on("SIGINT", async () => {
      console.log("\nStopping watch mode...");
      assetsWatcher.close();
      await jsContext.dispose();
      await cssContext.dispose();
      process.exit(0);
    });
  } catch (err) {
    console.error("Watch mode failed:", err);
    process.exit(1);
  }
}

(async () => {
  if (watch) {
    await build();
    await startWatch();
  } else {
    await build();
  }
})();

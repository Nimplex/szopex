const path = require("path");
const glob = require("glob");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const { PurgeCSSPlugin } = require("purgecss-webpack-plugin");
const { WebpackManifestPlugin } = require("webpack-manifest-plugin");
const globAll = require("glob-all");

// Function to generate entries dynamically
function getEntries() {
  const entries = {};
  
  // Get all CSS files
  const cssFiles = glob.sync('./resources/css/**/*.css');
  cssFiles.forEach(file => {
    const name = path.basename(file, '.css');
    entries[name] = path.resolve(__dirname, file); // Use absolute path
  });
  
  // Get all JS files
  const jsFiles = glob.sync('./resources/js/**/*.js');
  jsFiles.forEach(file => {
    const name = path.basename(file, '.js');
    entries[name] = path.resolve(__dirname, file); // Use absolute path
  });
  
  return entries;
}

module.exports = (env, argv) => {
  const isProduction = argv.mode === "production";

  return {
    entry: getEntries(),

    output: {
      path: path.resolve(__dirname, "public"),
      filename: "_js/[name].[contenthash:8].js",
      assetModuleFilename: "assets/[name].[contenthash:8][ext]",
      publicPath: "/",
      clean: {
        keep: /^(?!_js|_css).*/,
      },
    },

    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: "babel-loader",
            options: {
              presets: ["@babel/preset-env"],
            },
          },
        },

        {
          test: /\.css$/,
          use: [MiniCssExtractPlugin.loader, "css-loader"],
        },

        {
          test: /\.(png|jpg|jpeg|gif|svg)$/i,
          type: "asset",
          parser: {
            dataUrlCondition: {
              maxSize: 8 * 1024, // Inline files < 8kb
            },
          },
        },

        {
          test: /\.(woff|woff2|eot|ttf|otf)$/i,
          type: "asset/resource",
        },
      ],
    },

    plugins: [
      new MiniCssExtractPlugin({
        filename: "_css/[name].[contenthash:8].css",
      }),

      // Creates manifest.json for PHP integration
      new WebpackManifestPlugin({
        fileName: "manifest.json",
        publicPath: "/",
      }),

     // ...(isProduction
     //   ? [
     //       new PurgeCSSPlugin({
     //         paths: globAll.sync([
     //           path.join(__dirname, "public/**/*.php"),
     //           path.join(__dirname, "resources/**/*.php"),
     //         ]),
     //         safelist: {
     //           standard: [
     //             /^htmx-/,
     //             /^active$/,
     //             /^show$/,
     //             /^hidden$/,
     //             /^error$/,
     //             /^success$/,
     //           ],
     //         },
     //       }),
     //     ]
     //   : []),
    ],

    optimization: {
      minimizer: [
        new TerserPlugin({
          terserOptions: {
            compress: {
              drop_console: isProduction,
            },
          },
        }),
        new CssMinimizerPlugin(),
      ],
    },

    devtool: isProduction ? "source-map" : "eval-source-map",

    stats: {
      colors: true,
      modules: false,
      children: false,
      chunks: false,
      chunkModules: false,
    },
  };
};

import { wordpressPlugin } from "@roots/vite-plugin";
import fs from "fs";
import { globSync } from "glob";
import { resolve } from "path";
import { defineConfig, loadEnv } from "vite";
import { viteStaticCopy } from "vite-plugin-static-copy";

/**
 * RAN Vite configuration
 *
 * @version 0.0.2
 * @author bnjmnrsh
 */

/**
 * Get entry points from glob patterns
 */
function getEntries() {
	const entries = {};
	const ignorePatterns = ["**/vendor/**", "**/node_modules/**"];

	// Process JS files
	const jsFiles = globSync("assets/src/**/*.js", {
		ignore: [...ignorePatterns, "**/*.min.js", "**/*-min.js"],
	});
	jsFiles.forEach((file) => {
		const key = file.replace("assets/src/", "").replace(/\.js$/, "");
		entries[key] = resolve(__dirname, file);
		console.log(`Adding JS entry: ${key} -> ${file}`);
	});

	// Process SCSS files
	const scssFiles = globSync("assets/src/**/*.scss", {
		ignore: ignorePatterns,
	});
	scssFiles.forEach((file) => {
		const key = file.replace("assets/src/", "").replace(/\.scss$/, "");
		entries[key] = resolve(__dirname, file);
		console.log(`Adding SCSS entry: ${key} -> ${file}`);
	});

	// Process CSS files (excluding minified and vendor files)
	const cssFiles = globSync("assets/src/**/*.css", {
		ignore: [...ignorePatterns, "**/*.min.css", "**/*-min.css"],
	});

	cssFiles.forEach((file) => {
		const key = file.replace("assets/src/", "").replace(/\.css$/, "");
		entries[key] = resolve(__dirname, file);
		console.log(`Adding CSS entry: ${key} -> ${file}`);
	});

	return entries;
}

/**
 * viteStaticCopy options
 */
const staticCopyOptions = {
	targets: [],
	hook: "writeBundle", // Ensure this runs after the build is complete
	copySync: true, // Use sync version to avoid race conditions
	overwrite: true, // Explicitly allow overwriting
	flatten: false, // Preserve directory structure
};

/**
 * Vite configuration
 */
export default defineConfig(({ command, mode }) => {
	// Define possible .env file locations
	const envDirs = [
		process.cwd(), // Current directory (plugin root)
		resolve(process.cwd(), ".."), // Parent directory (plugins/)
		resolve(process.cwd(), "../.."), // wp-content directory
	];

	// Load environment variables from multiple locations
	let env = {};
	for (const envDir of envDirs) {
		try {
			env = { ...env, ...loadEnv(mode, envDir, "") };
			// If we found a .env file, use this directory for subsequent loads
			process.env.RAN_VITE_ENV_DIR = envDir;
			break;
		} catch (e) {
			// Continue to next directory if .env not found
			continue;
		}
	}

	// Development server configuration
	const isDev = command === "serve";
	const host = env.RAN_VITE_DEV_HOST || "0.0.0.0";
	const protocol = env.RAN_VITE_DEV_PROTOCOL || "http";
	const port = parseInt(env.RAN_VITE_DEV_PORT || "3000", 10);

	console.log(
		`Using environment from: ${process.env.RAN_VITE_ENV_DIR || "default"}`,
	);

	const assetTypes =
		"{png,jpg,jpeg,gif,svg,bmp,webp,ico,apng,avif,woff,woff2,eot,ttf,otf,ttc,mp4,webm,ogg,mp3,wav,flac,aac,mov}";

	// Function to add vendor directories and their contents
	const addVendorPaths = (basePath, destBase) => {
		const vendorPaths = [];

		if (!fs.existsSync(basePath)) return vendorPaths;

		// Get all subdirectories in the vendor directory
		const vendorDirs = globSync(`${basePath}/*`, {
			onlyDirectories: true,
		});

		// For each subdirectory, create a specific copy target
		vendorDirs.forEach((dir) => {
			const dirName = dir.split("/").pop();
			vendorPaths.push({
				src: `${dir}/**/*`,
				dest: `${destBase}/${dirName}`,
			});
		});

		// Also copy any files directly in the vendor directory
		vendorPaths.push({
			src: `${basePath}/*.*`,
			dest: destBase,
		});

		return vendorPaths;
	};

	// Get all vendor paths
	const getVendorPaths = () => {
		const vendorLocations = [
			// Root level vendor directories
			["admin/vendor", "admin/vendor"],
			["public/vendor", "public/vendor"],
			// Nested vendor directories
			["assets/src/admin/vendor", "admin/vendor"],
			["assets/src/public/vendor", "public/vendor"],
			["assets/src/admin/js/vendor", "admin/js/vendor"],
			["assets/src/public/js/vendor", "public/js/vendor"],
			["assets/src/admin/styles/vendor", "admin/styles/vendor"],
			["assets/src/public/styles/vendor", "public/styles/vendor"],
		];

		let vendorPaths = [];
		vendorLocations.forEach(([src, dest]) => {
			const paths = addVendorPaths(src, dest);
			if (paths && paths.length > 0) {
				vendorPaths = [...vendorPaths, ...paths];
			}
		});

		// Fallback for common locations if no vendor paths found
		if (vendorPaths.length === 0) {
			const fallbackPaths = [
				...addVendorPaths("admin/vendor", "admin/vendor"),
				...addVendorPaths("public/vendor", "public/vendor"),
				...addVendorPaths("assets/src/admin/vendor", "admin/vendor"),
				...addVendorPaths("assets/src/public/vendor", "public/vendor"),
			].filter(Boolean);
			vendorPaths = [...vendorPaths, ...fallbackPaths];
		}

		// Filter to only include directories that exist and have files
		return vendorPaths.filter(({ src }) => {
			const dirPath = src.split("/**/*")[0];
			if (!fs.existsSync(dirPath)) return false;

			try {
				return globSync(src).length > 0;
			} catch (error) {
				console.warn(
					`Warning: Error checking files in ${dirPath}:`,
					error,
				);
				return false;
			}
		});
	};

	return {
		root: __dirname,
		base: isDev ? `${protocol}://${host}:${port}/` : "./",
		publicDir: false,
		build: {
			outDir: "assets/dist",
			emptyOutDir: true,
			sourcemap: isDev,
			rollupOptions: {
				input: getEntries(),
				output: {
					// Maintain original path structure with .min.js
					entryFileNames: (chunkInfo) => {
						if (!chunkInfo.facadeModuleId) return "[name].min.js";
						const relativePath = chunkInfo.facadeModuleId
							.split("src/")[1]
							.replace(/\.(js|ts)x?$/, ".min.js");
						return relativePath;
					},
					// Handle CSS output with .min.css
					assetFileNames: (assetInfo) => {
						if (!assetInfo.name)
							return "assets/[name]-[hash][extname]";

						// Handle CSS files (both .css and .scss)
						if (
							assetInfo.name.endsWith(".css") ||
							assetInfo.name.endsWith(".scss")
						) {
							const baseName = assetInfo.name
								.replace(/^.*[\\/]/, "") // Remove path
								.replace(/\.(css|scss)$/, ".min.css"); // Replace extension

							// Preserve directory structure but remove 'src/' and add .min.css
							const relativePath = assetInfo.name
								.replace(/^assets\\src\\?|^src\\?/, "") // Remove leading src/
								.replace(/\.[^.]*$/, ".min.css"); // Replace extension

							return relativePath;
						}

						// For other assets (images, fonts, etc.)
						if (
							assetInfo.name.match(
								new RegExp(`\.${assetTypes}$`, "i"),
							)
						) {
							return "assets/[name]-[hash][extname]";
						}

						// Default for other assets
						return "assets/[name]-[hash][extname]";
					},
				},
			},
		},

		plugins: [
			wordpressPlugin({
				hmr: {
					enabled: true,
				},
			}),

			/**
			 * Copy static assets such as images, fonts, etc. that aren't in vendor directories
			 *
			 * The order of operations is important to ensure that there are no directory creation conflicts at the fs level.
			 * This is set by the `hook` option:
			 * 1. buildStart: Copy static assets such as images, fonts, etc. that aren't in vendor directories
			 * 2. writeBundle: Handle vendor files
			 * 3. writeBundle: Copy minified assets (CSS/JS) without processing
			 *
			 * @see https://vite.dev/guide/api-plugin.html#hook
			 */

			// buildStart: Copy static assets such as images, fonts, etc. that aren't in vendor directories
			viteStaticCopy({
				...staticCopyOptions,
				hook: "buildStart", // Run at the start of the build
				targets: (() => {
					const targets = [];
					// Only add admin assets if the directory exists
					const adminAssetsDir = resolve(
						__dirname,
						"assets/src/admin",
					);
					if (fs.existsSync(adminAssetsDir)) {
						targets.push({
							src: `assets/src/admin/**/*.${assetTypes}`,
							dest: "admin",
							rename: (name, extension, fullPath) => {
								return fullPath.replace(
									/^.*?assets\/src\/admin[\\\/]?/,
									"",
								);
							},
							overwrite: true,
						});
					}

					// Only add public assets if the directory exists
					const publicAssetsDir = resolve(
						__dirname,
						"assets/src/public",
					);
					if (fs.existsSync(publicAssetsDir)) {
						targets.push({
							src: `assets/src/public/**/*.${assetTypes}`,
							dest: "public",
							rename: (name, extension, fullPath) => {
								return fullPath.replace(
									/^.*?assets\/src\/public[\\\/]?/,
									"",
								);
							},
							overwrite: true,
						});
					}

					// Add a catch-all for other assets in src
					targets.push({
						src: `assets/src/**/*.${assetTypes}`,
						dest: "./",
						rename: (name, extension, fullPath) => {
							return fullPath.replace(
								/^.*?assets\/src[\\\/]?/,
								"",
							);
						},
						overwrite: true,
					});

					// Filter out any patterns that don't match any files
					return targets.filter((target) => {
						try {
							const files = globSync(target.src, { nodir: true });
							return files.length > 0;
						} catch (e) {
							return false;
						}
					});
				})(),
			}),

			// writeBundle: Handle vendor files
			viteStaticCopy({
				...staticCopyOptions,
				hook: "writeBundle", // Run at the end of the build (default)
				targets: getVendorPaths(),
			}),

			// writeBundle: Copy minified assets (CSS/JS) without processing
			viteStaticCopy({
				...staticCopyOptions,
				hook: "writeBundle", // Run at the end of the build (default)
				targets: [
					// Only include minified files if they exist and are not in vendor directories
					...(fs.existsSync("assets/src")
						? [
								// Copy *.min.* files (non-vendor only)
								...(() => {
									const files = globSync(
										"assets/src/**/*.min.{js,css}",
										{
											nodir: true,
											ignore: ["**/vendor/**"], // Skip vendor files
										},
									);
									if (files.length === 0) return [];
									return files.map((file) => {
										// Get the relative path from assets/src
										const relativePath = file.replace(
											/^assets\/src\//,
											"",
										);
										// Get the directory path (remove filename)
										const dirPath = relativePath.replace(
											/\/[^/]+$/,
											"",
										);
										return {
											src: file,
											dest: dirPath, // Destination directory
											rename: path.basename(relativePath), // Keep original filename
										};
									});
								})(),
								// Copy *-min.* files (non-vendor only)
								...(() => {
									const files = globSync(
										"assets/src/**/*-min.{js,css}",
										{
											nodir: true,
											ignore: ["**/vendor/**"], // Skip vendor files
										},
									);
									if (files.length === 0) return [];
									return files.map((file) => {
										// Get the relative path from assets/src
										const relativePath = file.replace(
											/^assets\/src\//,
											"",
										);
										// Get the directory path (remove filename)
										const dirPath = relativePath.replace(
											/\/[^/]+$/,
											"",
										);
										return {
											src: file,
											dest: dirPath, // Destination directory
											rename: path.basename(relativePath), // Keep original filename
										};
									});
								})(),
							]
						: []),
				]
					.filter(Boolean)
					.filter((item) => {
						// Skip if source doesn't exist
						if (!fs.existsSync(item.src)) return false;
						// Skip if source and destination are the same
						if (item.src === item.dest) return false;
						return true;
					}),
			}),
		],
		server: {
			host: "0.0.0.0",
			port: port,
			strictPort: true,
			https: protocol === "https",
			hmr: isDev
				? {
						protocol: protocol.includes("https") ? "wss" : "ws",
						host: host,
						port: port,
					}
				: undefined,
			cors: isDev,
		},
	};
});

import { resolve } from "node:path";
import { defineConfig } from "vite";

const asset = ( path ) => resolve( import.meta.dirname, path );

/**
 * Build stable WordPress asset paths. A starter should make it obvious which
 * source entry produces each committed runtime asset.
 */
export default defineConfig( {
	build: {
		outDir: asset( "assets/dist" ),
		emptyOutDir: true,
		rollupOptions: {
			input: {
				"admin/js/admin": asset( "assets/src/admin/js/admin.js" ),
				"admin/styles/admin": asset( "assets/src/admin/styles/admin.scss" ),
				"admin/styles/example_feature": asset( "assets/src/admin/styles/example_feature.scss" ),
				"public/js/public": asset( "assets/src/public/js/public.js" ),
				"public/styles/public": asset( "assets/src/public/styles/public.scss" ),
			},
			output: {
				entryFileNames: "[name].min.js",
				assetFileNames: ( assetInfo ) => {
					if ( assetInfo.name?.endsWith( ".css" ) ) {
						return "[name].min.css";
					}

					return "assets/[name]-[hash][extname]";
				},
			},
		},
	},
} );

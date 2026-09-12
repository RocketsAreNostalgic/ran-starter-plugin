import ranWordPress from "@rocketsarenostalgic/quality-config/eslint/wordpress";

export default [
	...ranWordPress,
	{
		files: [ "assets/src/**/*.{js,mjs,ts,mts}" ],
		languageOptions: {
			globals: {
				$: "readonly",
				jQuery: "readonly",
			},
		},
		settings: {
			react: {
				version: "999.999.999",
			},
		},
		rules: {
			"no-console": "warn",
		},
	},
];

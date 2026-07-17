import wordpress from "@wordpress/eslint-plugin";

export default [
	...wordpress.configs.recommended,
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

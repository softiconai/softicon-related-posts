module.exports = {
	settings: {
		react: {
			version: 'detect'
		}
	},
	env: {
		browser: true,
		commonjs: true,
		es6: true,
		node: true
	},
	extends: [
		'eslint:recommended',
		'plugin:react/recommended'
	],
	globals: {
		wp: 'readonly',
		alrpBlockData: 'readonly',
		alrpSettingsData: 'readonly'
	},
	parserOptions: {
		ecmaFeatures: {
			jsx: true
		},
		ecmaVersion: 12,
		sourceType: 'module'
	},
	plugins: [
		'react'
	],
	rules: {
		'no-console': 'warn',
		'no-unused-vars': 'warn',
		'react/prop-types': 'off',
		'react/react-in-jsx-scope': 'off',
		'react/display-name': 'off',
		'no-unsafe-optional-chaining': 'off'
	}
}

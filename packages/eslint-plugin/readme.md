# ESLint Plugin


[@byteever/eslint-plugin](https://github.com/byteever/eslint-plugin) is built on top of [`@wordpress/eslint-plugin/recommended`](https://github.com/WordPress/gutenberg/tree/master/packages/eslint-plugin) ruleset to meet ByteEver's coding standards.

## Installation

Install [@byteever/eslint-plugin](https://github.com/byteever/eslint-plugin) as a development dependency of your project:

```sh
npm install @byteever/eslint-plugin --save-dev
```

## Usage

In order to use this config, add this configuration to your `package.json`:

```json
{
    "eslintConfig": {
        "extends": "@byteever/eslint-plugin/recommended"
    }
}
```

Or add a `.eslintrc.js` file to your project root containing:
```js
module.exports = {
	extends: ['@byteever/eslint-plugin/recommended'],
};
```

## Editor integration

If you want to use prettier in your code editor, you'll need to create a `.prettierrc.js` file at the root of your project with the following:

```js
module.exports = require("@byteever/eslint-plugin/recommended");
```

We recommend turning on VSCode settings to automatically run `eslint --fix` on save.

```json
"editor.codeActionsOnSave": {
   "source.fixAll.eslint": true,
}
```

This will automagically format your code once you save. You don't need VSCode prettier extension enabled or running on save as eslint will automatically run `prettier` for you.


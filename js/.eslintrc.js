module.exports = {
  env: {
    browser: true,
    es6: true,
    node: true
  },
  extends: 'standard',
  parserOptions: {
    ecmaVersion: '2020',
    sourceType: 'module'
  },
  globals: {
    atk: true,
    $: true,
    google: true
  },
  rules: {
    semi: ['error', 'always']
  }
};

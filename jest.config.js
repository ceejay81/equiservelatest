export default {
  testEnvironment: 'node',
  transform: {},
  testMatch: [
    '**/__tests__/**/*.test.js',
    '**/?(*.)+(spec|test).js'
  ],
  collectCoverageFrom: [
    'electron/**/*.js',
    '!electron/**/*.test.js',
    '!electron/test-*.js'
  ],
  coverageDirectory: 'coverage',
  verbose: true
};

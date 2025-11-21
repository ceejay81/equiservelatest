/**
 * Property-Based Tests for Configuration Manager
 * 
 * These tests verify universal properties that should hold across all valid executions.
 * Using fast-check for property-based testing with 100+ iterations per property.
 */

import { describe, test, expect, beforeEach, afterEach, jest } from '@jest/globals';
import fc from 'fast-check';
import ConfigManager from '../config-manager.js';
import path from 'path';
import os from 'os';

describe('Configuration Manager - Property-Based Tests', () => {
  let manager;
  let originalPlatform;
  let originalEnv;

  beforeEach(() => {
    manager = new ConfigManager();
    originalPlatform = process.platform;
    originalEnv = { ...process.env };
  });

  afterEach(() => {
    // Restore original platform and environment
    Object.defineProperty(process, 'platform', {
      value: originalPlatform,
      writable: true,
      configurable: true
    });
    process.env = originalEnv;
  });

  /**
   * Feature: electron-desktop-conversion, Property 10: Platform-appropriate paths
   * Validates: Requirements 7.5
   * 
   * For any platform (Windows, macOS, Linux), the user data directory should be 
   * located in the platform's standard application data location
   */
  describe('Property 10: Platform-appropriate paths', () => {
    /**
     * Generator for valid platform identifiers
     */
    const platformArbitrary = fc.constantFrom('win32', 'darwin', 'linux');

    /**
     * Generator for valid app names (alphanumeric with hyphens)
     */
    const appNameArbitrary = fc.stringMatching(/^[a-z][a-z0-9-]{2,30}$/);

    test('getUserDataPath returns platform-appropriate location for all platforms', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            // Create a new manager instance to pick up the platform change
            const testManager = new ConfigManager();
            const userDataPath = testManager.getUserDataPath();

            // Verify the path is not empty
            expect(userDataPath).toBeTruthy();
            expect(typeof userDataPath).toBe('string');
            expect(userDataPath.length).toBeGreaterThan(0);

            // Verify platform-specific path conventions
            const homeDir = os.homedir();
            
            switch (platform) {
              case 'win32':
                // Windows: Should be in AppData\Roaming or APPDATA
                const expectedWinPath = process.env.APPDATA || path.join(homeDir, 'AppData', 'Roaming');
                expect(userDataPath).toContain(expectedWinPath.replace(/\\/g, path.sep));
                break;
              
              case 'darwin':
                // macOS: Should be in ~/Library/Application Support
                expect(userDataPath).toContain(path.join(homeDir, 'Library', 'Application Support'));
                break;
              
              case 'linux':
                // Linux: Should be in ~/.config
                expect(userDataPath).toContain(path.join(homeDir, '.config'));
                break;
            }

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('getUserDataPath returns absolute paths for all platforms', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const userDataPath = testManager.getUserDataPath();

            // Verify the path is absolute
            expect(path.isAbsolute(userDataPath)).toBe(true);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('getUserDataPath is consistent across multiple calls', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            
            // Call multiple times
            const path1 = testManager.getUserDataPath();
            const path2 = testManager.getUserDataPath();
            const path3 = testManager.getUserDataPath();

            // All calls should return the same path
            expect(path1).toBe(path2);
            expect(path2).toBe(path3);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('derived paths (database, storage, logs) are within user data directory', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const userDataPath = testManager.getUserDataPath();
            const dbPath = testManager.getDatabasePath();
            const storagePath = testManager.getStoragePath();
            const logsPath = testManager.getLogsPath();

            // All derived paths should be within the user data directory
            expect(dbPath).toContain(userDataPath);
            expect(storagePath).toContain(userDataPath);
            expect(logsPath).toContain(userDataPath);

            // All derived paths should be absolute
            expect(path.isAbsolute(dbPath)).toBe(true);
            expect(path.isAbsolute(storagePath)).toBe(true);
            expect(path.isAbsolute(logsPath)).toBe(true);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('user data path does not contain invalid path characters', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const userDataPath = testManager.getUserDataPath();

            // Path should not contain null bytes or other invalid characters
            expect(userDataPath).not.toContain('\0');
            
            // On Windows, check for invalid characters
            if (platform === 'win32') {
              const invalidChars = ['<', '>', '"', '|', '?', '*'];
              for (const char of invalidChars) {
                expect(userDataPath).not.toContain(char);
              }
            }

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('getFallbackUserDataPath follows platform conventions', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const fallbackPath = testManager.getFallbackUserDataPath();

            // Verify the path is not empty
            expect(fallbackPath).toBeTruthy();
            expect(typeof fallbackPath).toBe('string');

            // Verify it's an absolute path
            expect(path.isAbsolute(fallbackPath)).toBe(true);

            // Verify platform-specific conventions
            const homeDir = os.homedir();
            
            switch (platform) {
              case 'win32':
                // Should contain AppData\Roaming
                expect(
                  fallbackPath.includes('AppData') && 
                  fallbackPath.includes('Roaming')
                ).toBe(true);
                break;
              
              case 'darwin':
                // Should contain Library/Application Support
                expect(fallbackPath).toContain('Library');
                expect(fallbackPath).toContain('Application Support');
                break;
              
              case 'linux':
                // Should contain .config
                expect(fallbackPath).toContain('.config');
                break;
            }

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('database path has correct file extension', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const dbPath = testManager.getDatabasePath();

            // Database path should end with .sqlite
            expect(dbPath).toMatch(/\.sqlite$/);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('storage and logs paths are distinct subdirectories', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          async (platform) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const storagePath = testManager.getStoragePath();
            const logsPath = testManager.getLogsPath();

            // Storage and logs should be different paths
            expect(storagePath).not.toBe(logsPath);

            // Neither should be a parent of the other
            expect(storagePath).not.toContain(logsPath);
            expect(logsPath).not.toContain(storagePath);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('generated env file contains correct database path', async () => {
      await fc.assert(
        fc.asyncProperty(
          platformArbitrary,
          fc.integer({ min: 3000, max: 9000 }),
          async (platform, port) => {
            // Mock the platform
            Object.defineProperty(process, 'platform', {
              value: platform,
              writable: true,
              configurable: true
            });

            const testManager = new ConfigManager();
            const envContent = testManager.generateEnvFile(port);
            const dbPath = testManager.getDatabasePath();

            // Env file should contain the database path
            expect(envContent).toContain(dbPath);
            expect(envContent).toContain(`DB_DATABASE=${dbPath}`);

            // Env file should contain the port
            expect(envContent).toContain(`APP_URL=http://localhost:${port}`);

            return true;
          }
        ),
        { numRuns: 100 }
      );
    });
  });
});

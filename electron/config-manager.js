/**
 * Configuration Manager
 * 
 * Manages application configuration and environment setup.
 * Handles platform-specific paths, .env file generation, and user preferences.
 */

import path from 'path';
import os from 'os';

// Dynamically import electron only when available
let electronApp = null;
try {
  const electron = await import('electron');
  electronApp = electron.app;
} catch (error) {
  // Electron not available (e.g., in test environment)
  electronApp = null;
}

class ConfigManager {
  constructor() {
    this.userDataPath = null;
  }

  /**
   * Get the user data directory path based on the platform
   * 
   * Returns platform-appropriate locations:
   * - Windows: %APPDATA%/app-name
   * - macOS: ~/Library/Application Support/app-name
   * - Linux: ~/.config/app-name
   * 
   * @returns {string} User data directory path
   */
  getUserDataPath() {
    if (this.userDataPath) {
      return this.userDataPath;
    }

    // Use Electron's app.getPath('userData') which handles platform-specific paths
    // This follows platform conventions:
    // - Windows: C:\Users\<user>\AppData\Roaming\<app-name>
    // - macOS: ~/Library/Application Support/<app-name>
    // - Linux: ~/.config/<app-name>
    try {
      if (electronApp && electronApp.getPath) {
        this.userDataPath = electronApp.getPath('userData');
      } else {
        // Fallback if app is not ready or in test environment
        this.userDataPath = this.getFallbackUserDataPath();
      }
    } catch (error) {
      // Fallback if app is not ready or in test environment
      this.userDataPath = this.getFallbackUserDataPath();
    }

    return this.userDataPath;
  }

  /**
   * Get fallback user data path when Electron app is not available
   * Used primarily in testing environments
   * 
   * @returns {string} Fallback user data path
   */
  getFallbackUserDataPath() {
    const platform = process.platform;
    const homeDir = os.homedir();
    const appName = 'laravel-electron-app';

    switch (platform) {
      case 'win32':
        // Windows: %APPDATA%\app-name
        return path.join(process.env.APPDATA || path.join(homeDir, 'AppData', 'Roaming'), appName);
      
      case 'darwin':
        // macOS: ~/Library/Application Support/app-name
        return path.join(homeDir, 'Library', 'Application Support', appName);
      
      case 'linux':
      default:
        // Linux: ~/.config/app-name
        return path.join(homeDir, '.config', appName);
    }
  }

  /**
   * Get the database file path
   * 
   * @returns {string} Database file path
   */
  getDatabasePath() {
    return path.join(this.getUserDataPath(), 'database.sqlite');
  }

  /**
   * Get the storage directory path
   * 
   * @returns {string} Storage directory path
   */
  getStoragePath() {
    return path.join(this.getUserDataPath(), 'storage');
  }

  /**
   * Get the logs directory path
   * 
   * @returns {string} Logs directory path
   */
  getLogsPath() {
    return path.join(this.getUserDataPath(), 'logs');
  }

  /**
   * Generate desktop-specific .env file content
   * 
   * @param {number} port - PHP server port
   * @returns {string} .env file content
   */
  generateEnvFile(port) {
    const userDataPath = this.getUserDataPath();
    const dbPath = this.getDatabasePath();

    return `APP_NAME="Laravel Electron App"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:${port}

DB_CONNECTION=sqlite
DB_DATABASE=${dbPath}

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
FILESYSTEM_DISK=local

LOG_CHANNEL=single
LOG_LEVEL=error
LOG_PATH=${this.getLogsPath()}
`;
  }

  /**
   * Save user preferences
   * 
   * @param {Object} preferences - User preferences object
   */
  saveUserPreferences(preferences) {
    // TODO: Implement preferences saving
    throw new Error('Not implemented');
  }

  /**
   * Load user preferences
   * 
   * @returns {Object} User preferences object
   */
  loadUserPreferences() {
    // TODO: Implement preferences loading
    throw new Error('Not implemented');
  }
}

export default ConfigManager;

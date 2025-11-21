/**
 * Error Handler
 * 
 * Centralized error handling, logging, and user notifications.
 */

import { dialog } from 'electron';
import fs from 'fs/promises';
import path from 'path';
import { existsSync } from 'fs';

class ErrorHandler {
  constructor(configManager) {
    this.configManager = configManager;
    this.logsPath = configManager.getLogsPath();
    this.maxLogSize = 10 * 1024 * 1024; // 10MB
  }

  /**
   * Initialize error handler
   */
  async initialize() {
    // Ensure logs directory exists
    await fs.mkdir(this.logsPath, { recursive: true });

    // Set up global error handlers
    process.on('uncaughtException', (error) => {
      this.handleCriticalError('Uncaught Exception', error);
    });

    process.on('unhandledRejection', (reason, promise) => {
      this.handleCriticalError('Unhandled Rejection', reason);
    });
  }

  /**
   * Handle critical errors that crash the application
   */
  async handleCriticalError(type, error) {
    console.error(`${type}:`, error);
    
    await this.logError(type, error);
    
    dialog.showErrorBox(
      'Application Error',
      `A critical error occurred:\n\n${error.message}\n\nThe application will now close. Please check the logs for more details.`
    );
    
    process.exit(1);
  }

  /**
   * Handle server startup errors
   */
  async handleServerStartupError(error) {
    console.error('Server startup error:', error);
    
    await this.logError('Server Startup', error);
    
    const result = await dialog.showMessageBox({
      type: 'error',
      title: 'Server Startup Failed',
      message: 'Failed to start the application server',
      detail: `Error: ${error.message}\n\nPlease try restarting the application. If the problem persists, check the logs.`,
      buttons: ['Quit', 'View Logs'],
      defaultId: 0
    });

    if (result.response === 1) {
      // Open logs folder
      const { shell } = await import('electron');
      shell.openPath(this.logsPath);
    }
  }

  /**
   * Handle server crash
   */
  async handleServerCrash(code, signal) {
    console.error(`Server crashed with code ${code} and signal ${signal}`);
    
    await this.logError('Server Crash', new Error(`Server exited with code ${code} and signal ${signal}`));
    
    const result = await dialog.showMessageBox({
      type: 'error',
      title: 'Server Crashed',
      message: 'The application server has stopped unexpectedly',
      detail: 'Would you like to restart the application?',
      buttons: ['Quit', 'Restart'],
      defaultId: 1
    });

    if (result.response === 1) {
      // Restart application
      const { app } = await import('electron');
      app.relaunch();
      app.exit(0);
    } else {
      const { app } = await import('electron');
      app.quit();
    }
  }

  /**
   * Handle database errors
   */
  async handleDatabaseError(error) {
    console.error('Database error:', error);
    
    await this.logError('Database', error);
    
    await dialog.showMessageBox({
      type: 'error',
      title: 'Database Error',
      message: 'A database error occurred',
      detail: `Error: ${error.message}\n\nPlease check the logs for more details.`,
      buttons: ['OK']
    });
  }

  /**
   * Handle PHP errors
   */
  async handlePHPError(errorOutput) {
    console.error('PHP error:', errorOutput);
    
    await this.logError('PHP', new Error(errorOutput));
    
    // Only show dialog for fatal errors
    if (errorOutput.includes('Fatal error') || errorOutput.includes('Parse error')) {
      await dialog.showMessageBox({
        type: 'error',
        title: 'PHP Error',
        message: 'A PHP error occurred',
        detail: errorOutput.substring(0, 500), // Limit length
        buttons: ['OK']
      });
    }
  }

  /**
   * Show user-friendly error message
   */
  async showError(title, message, detail = null) {
    await dialog.showMessageBox({
      type: 'error',
      title,
      message,
      detail,
      buttons: ['OK']
    });
  }

  /**
   * Log error to file
   */
  async logError(category, error) {
    const timestamp = new Date().toISOString();
    const logEntry = {
      timestamp,
      category,
      message: error.message || String(error),
      stack: error.stack || null,
      platform: process.platform,
      version: process.env.npm_package_version || 'unknown'
    };

    const logLine = `[${timestamp}] [${category}] ${logEntry.message}\n${logEntry.stack || ''}\n\n`;
    
    try {
      const logFile = path.join(this.logsPath, 'error.log');
      
      // Check log file size and rotate if needed
      if (existsSync(logFile)) {
        const stats = await fs.stat(logFile);
        if (stats.size > this.maxLogSize) {
          await this.rotateLog(logFile);
        }
      }
      
      await fs.appendFile(logFile, logLine);
    } catch (err) {
      console.error('Failed to write to log file:', err);
    }
  }

  /**
   * Rotate log file
   */
  async rotateLog(logFile) {
    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
    const archiveFile = path.join(this.logsPath, `error-${timestamp}.log`);
    
    try {
      await fs.rename(logFile, archiveFile);
      console.log(`Log file rotated to: ${archiveFile}`);
    } catch (err) {
      console.error('Failed to rotate log file:', err);
    }
  }

  /**
   * Log info message
   */
  async logInfo(category, message) {
    const timestamp = new Date().toISOString();
    const logLine = `[${timestamp}] [${category}] ${message}\n`;
    
    try {
      const logFile = path.join(this.logsPath, 'app.log');
      await fs.appendFile(logFile, logLine);
    } catch (err) {
      console.error('Failed to write to log file:', err);
    }
  }
}

export default ErrorHandler;

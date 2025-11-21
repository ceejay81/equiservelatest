/**
 * Electron Main Process
 * 
 * This is the entry point for the Electron application.
 * It manages the application lifecycle, PHP server, windows, and native features.
 */

import { app, ipcMain, globalShortcut } from 'electron';
import path from 'path';
import { fileURLToPath } from 'url';

// Import managers
import PHPServerManager from './php-server.js';
import ConfigManager from './config-manager.js';
import DatabaseManager from './database-manager.js';
import WindowManager from './window-manager.js';
import ErrorHandler from './error-handler.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Manager instances
let phpServer;
let configManager;
let databaseManager;
let windowManager;
let errorHandler;

// Application state
let isInitialized = false;

/**
 * Initialize the application
 */
async function initializeApp() {
  if (isInitialized) {
    return;
  }

  try {
    console.log('Initializing application...');

    // 1. Initialize configuration manager
    configManager = new ConfigManager();
    console.log('✓ Configuration manager initialized');

    // 2. Initialize error handler
    errorHandler = new ErrorHandler(configManager);
    await errorHandler.initialize();
    console.log('✓ Error handler initialized');

    // 3. Initialize database manager
    databaseManager = new DatabaseManager(configManager);
    const isFirstRun = !(await databaseManager.initialize());
    console.log(`✓ Database initialized (first run: ${isFirstRun})`);

    // 4. Initialize PHP server
    phpServer = new PHPServerManager();
    
    // Register server crash handler
    phpServer.onServerExit((code, signal) => {
      if (code !== 0 && code !== null) {
        errorHandler.handleServerCrash(code, signal);
      }
    });

    console.log('✓ PHP server manager initialized');

    // 5. Initialize window manager
    windowManager = new WindowManager();
    console.log('✓ Window manager initialized');

    isInitialized = true;
    console.log('Application initialization complete');
  } catch (error) {
    console.error('Application initialization failed:', error);
    await errorHandler.handleCriticalError('Initialization', error);
  }
}

/**
 * Start the application
 */
async function startApp() {
  try {
    // Show loading window
    windowManager.createLoadingWindow();

    // Start PHP server
    console.log('Starting PHP server...');
    const serverURL = await phpServer.start();
    console.log(`✓ PHP server started at ${serverURL}`);

    // Create main window
    console.log('Creating main window...');
    windowManager.createMainWindow(serverURL);
    console.log('✓ Main window created');

    // Create system tray
    windowManager.createTray();
    console.log('✓ System tray created');

    // Start health check polling
    phpServer.startHealthCheckPolling(5000, () => {
      errorHandler.showError(
        'Server Error',
        'The application server has become unresponsive',
        'Please restart the application'
      );
    });

    // Log successful startup
    await errorHandler.logInfo('Startup', 'Application started successfully');
  } catch (error) {
    console.error('Application startup failed:', error);
    await errorHandler.handleServerStartupError(error);
    app.quit();
  }
}

/**
 * Shutdown the application gracefully
 */
async function shutdownApp() {
  console.log('Shutting down application...');

  try {
    // Stop health check polling
    if (phpServer) {
      phpServer.stopHealthCheckPolling();
    }

    // Stop PHP server
    if (phpServer) {
      await phpServer.stop();
      console.log('✓ PHP server stopped');
    }

    // Cleanup windows
    if (windowManager) {
      windowManager.cleanup();
      console.log('✓ Windows cleaned up');
    }

    // Unregister shortcuts
    globalShortcut.unregisterAll();

    // Log shutdown
    if (errorHandler) {
      await errorHandler.logInfo('Shutdown', 'Application shut down gracefully');
    }

    console.log('Application shutdown complete');
  } catch (error) {
    console.error('Error during shutdown:', error);
  }
}

/**
 * Register IPC handlers
 */
function registerIPCHandlers() {
  // Window controls
  ipcMain.on('window:minimize', () => {
    const window = windowManager.getMainWindow();
    if (window) window.minimize();
  });

  ipcMain.on('window:maximize', () => {
    const window = windowManager.getMainWindow();
    if (window) {
      if (window.isMaximized()) {
        window.unmaximize();
      } else {
        window.maximize();
      }
    }
  });

  ipcMain.on('window:close', () => {
    const window = windowManager.getMainWindow();
    if (window) window.hide();
  });

  // Application controls
  ipcMain.on('app:quit', () => {
    windowManager.setQuitting(true);
    app.quit();
  });

  // Get app version
  ipcMain.handle('app:getVersion', () => {
    return app.getVersion();
  });
}

/**
 * Register keyboard shortcuts
 */
function registerShortcuts() {
  // Quit application (Ctrl/Cmd + Q)
  globalShortcut.register('CommandOrControl+Q', () => {
    windowManager.setQuitting(true);
    app.quit();
  });

  // Toggle DevTools (Ctrl/Cmd + Shift + I) - development only
  if (process.env.NODE_ENV !== 'production') {
    globalShortcut.register('CommandOrControl+Shift+I', () => {
      const window = windowManager.getMainWindow();
      if (window) {
        window.webContents.toggleDevTools();
      }
    });
  }
}

// ============================================================================
// Electron App Event Handlers
// ============================================================================

// Single instance lock - prevent multiple instances
const gotTheLock = app.requestSingleInstanceLock();

if (!gotTheLock) {
  app.quit();
} else {
  app.on('second-instance', () => {
    // Someone tried to run a second instance, focus our window
    if (windowManager) {
      windowManager.showMainWindow();
    }
  });

  // App is ready
  app.whenReady().then(async () => {
    await initializeApp();
    registerIPCHandlers();
    registerShortcuts();
    await startApp();
  });
}

// All windows closed
app.on('window-all-closed', () => {
  // On macOS, keep app running in dock
  if (process.platform !== 'darwin') {
    app.quit();
  }
});

// App is activated (macOS)
app.on('activate', () => {
  if (windowManager && !windowManager.getMainWindow()) {
    startApp();
  } else if (windowManager) {
    windowManager.showMainWindow();
  }
});

// App is about to quit
app.on('before-quit', () => {
  if (windowManager) {
    windowManager.setQuitting(true);
  }
});

// App will quit
app.on('will-quit', async (event) => {
  event.preventDefault();
  await shutdownApp();
  app.exit(0);
});

// Handle uncaught errors
process.on('uncaughtException', (error) => {
  console.error('Uncaught exception:', error);
  if (errorHandler) {
    errorHandler.handleCriticalError('Uncaught Exception', error);
  }
});

process.on('unhandledRejection', (reason) => {
  console.error('Unhandled rejection:', reason);
  if (errorHandler) {
    errorHandler.handleCriticalError('Unhandled Rejection', reason);
  }
});

console.log('Electron main process initialized');
console.log(`Running in ${process.env.NODE_ENV || 'production'} mode`);

/**
 * Window Manager
 * 
 * Manages Electron windows including main window, loading screen, and system tray.
 */

import { BrowserWindow, Tray, Menu, nativeImage, app } from 'electron';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

class WindowManager {
  constructor() {
    this.mainWindow = null;
    this.loadingWindow = null;
    this.tray = null;
    this.isQuitting = false;
  }

  /**
   * Create the loading/splash screen window
   */
  createLoadingWindow() {
    this.loadingWindow = new BrowserWindow({
      width: 400,
      height: 300,
      frame: false,
      transparent: true,
      alwaysOnTop: true,
      resizable: false,
      webPreferences: {
        nodeIntegration: false,
        contextIsolation: true
      }
    });

    // Simple loading HTML
    const loadingHTML = `
      <!DOCTYPE html>
      <html>
      <head>
        <style>
          body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: white;
          }
          .container {
            text-align: center;
          }
          .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
          }
          @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
          h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
          }
          p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
          }
        </style>
      </head>
      <body>
        <div class="container">
          <div class="spinner"></div>
          <h1>EquiServe</h1>
          <p>Starting application...</p>
        </div>
      </body>
      </html>
    `;

    this.loadingWindow.loadURL(`data:text/html;charset=utf-8,${encodeURIComponent(loadingHTML)}`);
    this.loadingWindow.center();
  }

  /**
   * Close the loading window
   */
  closeLoadingWindow() {
    if (this.loadingWindow) {
      this.loadingWindow.close();
      this.loadingWindow = null;
    }
  }

  /**
   * Create the main application window
   * @param {string} serverURL - URL of the PHP server
   */
  createMainWindow(serverURL) {
    this.mainWindow = new BrowserWindow({
      width: 1280,
      height: 800,
      minWidth: 1024,
      minHeight: 600,
      show: false, // Don't show until ready
      webPreferences: {
        nodeIntegration: false,
        contextIsolation: true,
        preload: path.join(__dirname, 'preload.js')
      },
      icon: this.getAppIcon()
    });

    // Load the Laravel application
    this.mainWindow.loadURL(serverURL);

    // Show window when ready
    this.mainWindow.once('ready-to-show', () => {
      this.closeLoadingWindow();
      this.mainWindow.show();
      this.mainWindow.focus();
    });

    // Handle window close
    this.mainWindow.on('close', (event) => {
      if (!this.isQuitting) {
        event.preventDefault();
        this.mainWindow.hide();
        return false;
      }
    });

    // Handle window closed
    this.mainWindow.on('closed', () => {
      this.mainWindow = null;
    });

    // Open external links in default browser
    this.mainWindow.webContents.setWindowOpenHandler(({ url }) => {
      if (url.startsWith('http://') || url.startsWith('https://')) {
        require('electron').shell.openExternal(url);
      }
      return { action: 'deny' };
    });

    return this.mainWindow;
  }

  /**
   * Create system tray icon
   */
  createTray() {
    const icon = this.getAppIcon();
    this.tray = new Tray(icon);

    const contextMenu = Menu.buildFromTemplate([
      {
        label: 'Show EquiServe',
        click: () => {
          this.showMainWindow();
        }
      },
      {
        label: 'Hide EquiServe',
        click: () => {
          this.hideMainWindow();
        }
      },
      { type: 'separator' },
      {
        label: 'Quit',
        click: () => {
          this.isQuitting = true;
          app.quit();
        }
      }
    ]);

    this.tray.setToolTip('EquiServe');
    this.tray.setContextMenu(contextMenu);

    // Show window on tray click
    this.tray.on('click', () => {
      this.toggleMainWindow();
    });
  }

  /**
   * Get application icon path
   */
  getAppIcon() {
    const iconPath = path.join(__dirname, '..', 'build', 'icon.png');
    
    // Check if icon exists, otherwise return null (Electron will use default)
    try {
      return nativeImage.createFromPath(iconPath);
    } catch (error) {
      console.warn('Application icon not found, using default');
      return null;
    }
  }

  /**
   * Show the main window
   */
  showMainWindow() {
    if (this.mainWindow) {
      if (this.mainWindow.isMinimized()) {
        this.mainWindow.restore();
      }
      this.mainWindow.show();
      this.mainWindow.focus();
    }
  }

  /**
   * Hide the main window
   */
  hideMainWindow() {
    if (this.mainWindow) {
      this.mainWindow.hide();
    }
  }

  /**
   * Toggle main window visibility
   */
  toggleMainWindow() {
    if (this.mainWindow) {
      if (this.mainWindow.isVisible()) {
        this.hideMainWindow();
      } else {
        this.showMainWindow();
      }
    }
  }

  /**
   * Get the main window instance
   */
  getMainWindow() {
    return this.mainWindow;
  }

  /**
   * Set quitting flag
   */
  setQuitting(value) {
    this.isQuitting = value;
  }

  /**
   * Cleanup resources
   */
  cleanup() {
    if (this.tray) {
      this.tray.destroy();
      this.tray = null;
    }
    
    if (this.loadingWindow) {
      this.loadingWindow.close();
      this.loadingWindow = null;
    }
    
    if (this.mainWindow) {
      this.mainWindow.close();
      this.mainWindow = null;
    }
  }
}

export default WindowManager;

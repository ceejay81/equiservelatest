/**
 * Preload Script
 * 
 * Provides secure IPC communication between renderer and main process.
 * Uses contextBridge to expose limited APIs to the web page.
 */

import { contextBridge, ipcRenderer } from 'electron';

// Expose protected methods that allow the renderer process to use
// the ipcRenderer without exposing the entire object
contextBridge.exposeInMainWorld('electronAPI', {
  // Window controls
  minimizeWindow: () => ipcRenderer.send('window:minimize'),
  maximizeWindow: () => ipcRenderer.send('window:maximize'),
  closeWindow: () => ipcRenderer.send('window:close'),
  
  // Application controls
  quitApp: () => ipcRenderer.send('app:quit'),
  
  // Server status
  onServerStatus: (callback) => {
    ipcRenderer.on('server:status', (event, status) => callback(status));
  },
  
  // Update notifications
  onUpdateAvailable: (callback) => {
    ipcRenderer.on('update:available', (event, info) => callback(info));
  },
  onUpdateDownloaded: (callback) => {
    ipcRenderer.on('update:downloaded', (event, info) => callback(info));
  },
  installUpdate: () => ipcRenderer.send('update:install'),
  
  // Get app version
  getVersion: () => ipcRenderer.invoke('app:getVersion'),
  
  // Platform info
  getPlatform: () => process.platform,
  
  // Check if running in Electron
  isElectron: true
});

// Log that preload script has loaded
console.log('Electron preload script loaded');

/**
 * PHP Server Manager
 * 
 * Manages the embedded PHP development server lifecycle for the Laravel application.
 * Handles server startup, health checking, and graceful shutdown.
 */

import { spawn } from 'child_process';
import { createServer } from 'net';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

/**
 * PHPServerManager class
 * Manages the PHP development server process
 */
class PHPServerManager {
  constructor() {
    this.process = null;
    this.port = null;
    this.url = null;
    this.status = 'stopped';
    this.exitCallbacks = [];
    this.healthCheckInterval = null;
    this.startupTimeout = 30000; // 30 seconds default timeout
  }

  /**
   * Find an available port in the given range
   * @param {number} startPort - Starting port to check (default: 8000)
   * @param {number} endPort - Ending port to check (default: 9000)
   * @returns {Promise<number>} Available port number
   */
  async findAvailablePort(startPort = 8000, endPort = 9000) {
    for (let port = startPort; port <= endPort; port++) {
      if (await this.isPortAvailable(port)) {
        return port;
      }
    }
    throw new Error(`No available ports found between ${startPort} and ${endPort}`);
  }

  /**
   * Check if a specific port is available
   * @param {number} port - Port to check
   * @returns {Promise<boolean>} True if port is available
   */
  isPortAvailable(port) {
    return new Promise((resolve) => {
      const server = createServer();
      
      server.once('error', (err) => {
        if (err.code === 'EADDRINUSE') {
          resolve(false);
        } else {
          resolve(false);
        }
      });

      server.once('listening', () => {
        server.close();
        resolve(true);
      });

      server.listen(port, '127.0.0.1');
    });
  }

  /**
   * Start the PHP development server
   * @param {number|null} port - Port to use (null to auto-find)
   * @param {string} laravelPath - Path to Laravel application root
   * @returns {Promise<string>} Server URL
   */
  async start(port = null, laravelPath = null) {
    if (this.process) {
      throw new Error('PHP server is already running');
    }

    // Find available port if not specified
    if (!port) {
      this.port = await this.findAvailablePort();
    } else {
      this.port = port;
    }

    // Determine Laravel path
    const appPath = laravelPath || this.getDefaultLaravelPath();
    const publicPath = path.join(appPath, 'public');

    // Get PHP binary path
    const phpBinary = this.getPHPBinaryPath();

    // Build server URL
    this.url = `http://127.0.0.1:${this.port}`;

    // Spawn PHP server process
    this.status = 'starting';
    
    const args = [
      '-S',
      `127.0.0.1:${this.port}`,
      '-t',
      publicPath,
      path.join(publicPath, 'index.php')
    ];

    console.log(`Starting PHP server: ${phpBinary} ${args.join(' ')}`);

    this.process = spawn(phpBinary, args, {
      cwd: appPath,
      env: {
        ...process.env,
        APP_ENV: 'production',
        APP_DEBUG: 'false'
      },
      stdio: ['ignore', 'pipe', 'pipe']
    });

    // Handle process output
    this.process.stdout.on('data', (data) => {
      console.log(`[PHP Server] ${data.toString().trim()}`);
    });

    this.process.stderr.on('data', (data) => {
      console.error(`[PHP Server Error] ${data.toString().trim()}`);
    });

    // Handle process exit
    this.process.on('exit', (code, signal) => {
      console.log(`PHP server exited with code ${code} and signal ${signal}`);
      this.status = 'stopped';
      this.process = null;
      
      // Notify all registered callbacks
      this.exitCallbacks.forEach(callback => callback(code, signal));
    });

    this.process.on('error', (err) => {
      console.error('PHP server process error:', err);
      this.status = 'error';
    });

    // Wait for server to be ready
    await this.waitForReady();

    return this.url;
  }

  /**
   * Wait for the PHP server to be ready
   * @param {number} timeout - Timeout in milliseconds (default: 30000)
   * @returns {Promise<void>}
   */
  async waitForReady(timeout = null) {
    const maxWait = timeout || this.startupTimeout;
    const startTime = Date.now();
    const checkInterval = 500; // Check every 500ms

    while (Date.now() - startTime < maxWait) {
      if (await this.isReady()) {
        this.status = 'ready';
        console.log('PHP server is ready');
        return;
      }

      // Wait before next check
      await new Promise(resolve => setTimeout(resolve, checkInterval));
    }

    // Timeout reached
    this.stop();
    throw new Error(`PHP server failed to start within ${maxWait}ms`);
  }

  /**
   * Check if the PHP server is ready to accept requests
   * @returns {Promise<boolean>} True if server is ready
   */
  async isReady() {
    if (!this.process || this.status === 'stopped') {
      return false;
    }

    try {
      // Try to fetch from the server
      const response = await fetch(this.url, {
        method: 'GET',
        signal: AbortSignal.timeout(2000) // 2 second timeout
      });

      // Any response (even 404) means server is running
      return response.status !== undefined;
    } catch (error) {
      // Server not ready yet
      return false;
    }
  }

  /**
   * Stop the PHP server gracefully
   * @returns {Promise<void>}
   */
  async stop() {
    if (!this.process) {
      console.log('PHP server is not running');
      return;
    }

    console.log('Stopping PHP server...');

    return new Promise((resolve) => {
      const timeout = setTimeout(() => {
        console.log('PHP server did not stop gracefully, forcing kill');
        if (this.process) {
          this.process.kill('SIGKILL');
        }
        resolve();
      }, 5000); // 5 second grace period

      this.process.once('exit', () => {
        clearTimeout(timeout);
        this.status = 'stopped';
        this.process = null;
        this.port = null;
        this.url = null;
        
        // Clear health check interval if running
        if (this.healthCheckInterval) {
          clearInterval(this.healthCheckInterval);
          this.healthCheckInterval = null;
        }
        
        console.log('PHP server stopped');
        resolve();
      });

      // Send SIGTERM for graceful shutdown
      this.process.kill('SIGTERM');
    });
  }

  /**
   * Restart the PHP server
   * @returns {Promise<string>} Server URL
   */
  async restartServer() {
    const currentPort = this.port;
    const currentPath = this.laravelPath;
    
    await this.stop();
    return await this.start(currentPort, currentPath);
  }

  /**
   * Get the server URL
   * @returns {string|null} Server URL or null if not running
   */
  getServerURL() {
    return this.url;
  }

  /**
   * Get the current server status
   * @returns {string} Status: 'starting', 'ready', 'error', 'stopped'
   */
  getStatus() {
    return this.status;
  }

  /**
   * Register a callback for when the server exits
   * @param {Function} callback - Callback function (code, signal) => void
   */
  onServerExit(callback) {
    this.exitCallbacks.push(callback);
  }

  /**
   * Start health check polling
   * @param {number} interval - Polling interval in milliseconds (default: 5000)
   * @param {Function} onUnhealthy - Callback when server becomes unhealthy
   */
  startHealthCheckPolling(interval = 5000, onUnhealthy = null) {
    if (this.healthCheckInterval) {
      clearInterval(this.healthCheckInterval);
    }

    this.healthCheckInterval = setInterval(async () => {
      const healthy = await this.isReady();
      
      if (!healthy && this.status === 'ready') {
        console.error('PHP server health check failed');
        this.status = 'error';
        
        if (onUnhealthy) {
          onUnhealthy();
        }
      }
    }, interval);
  }

  /**
   * Stop health check polling
   */
  stopHealthCheckPolling() {
    if (this.healthCheckInterval) {
      clearInterval(this.healthCheckInterval);
      this.healthCheckInterval = null;
    }
  }

  /**
   * Get the default Laravel application path
   * @returns {string} Path to Laravel application
   */
  getDefaultLaravelPath() {
    // In development, Laravel is in the parent directory
    // In production, it will be in the resources directory
    if (process.env.NODE_ENV === 'production') {
      return path.join(process.resourcesPath, 'laravel');
    } else {
      return path.resolve(__dirname, '..');
    }
  }

  /**
   * Get the PHP binary path for the current platform
   * @returns {string} Path to PHP binary
   */
  getPHPBinaryPath() {
    const platform = process.platform;
    
    // In production, use bundled PHP binary
    if (process.env.NODE_ENV === 'production') {
      const phpDir = path.join(process.resourcesPath, 'php');
      
      if (platform === 'win32') {
        return path.join(phpDir, 'php.exe');
      } else {
        return path.join(phpDir, 'bin', 'php');
      }
    } else {
      // In development, use system PHP
      return platform === 'win32' ? 'php.exe' : 'php';
    }
  }
}

export default PHPServerManager;

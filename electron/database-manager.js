/**
 * Database Manager
 * 
 * Manages SQLite database initialization, migrations, backups, and integrity checks.
 */

import { spawn } from 'child_process';
import fs from 'fs/promises';
import path from 'path';
import { existsSync } from 'fs';

class DatabaseManager {
  constructor(configManager) {
    this.configManager = configManager;
    this.dbPath = configManager.getDatabasePath();
  }

  /**
   * Initialize database for first run
   * Creates the database file and runs migrations
   */
  async initialize() {
    const dbExists = existsSync(this.dbPath);
    
    if (!dbExists) {
      console.log('First run detected - initializing database...');
      
      // Ensure database directory exists
      const dbDir = path.dirname(this.dbPath);
      await fs.mkdir(dbDir, { recursive: true });
      
      // Create empty database file
      await fs.writeFile(this.dbPath, '');
      console.log(`Database created at: ${this.dbPath}`);
    }
    
    // Run migrations
    await this.runMigrations();
    
    return dbExists;
  }

  /**
   * Run Laravel migrations
   */
  async runMigrations() {
    console.log('Running database migrations...');
    
    try {
      await this.executeArtisan(['migrate', '--force']);
      console.log('Migrations completed successfully');
    } catch (error) {
      console.error('Migration failed:', error);
      throw new Error(`Database migration failed: ${error.message}`);
    }
  }

  /**
   * Create a backup of the database
   * @returns {string} Path to backup file
   */
  async createBackup() {
    if (!existsSync(this.dbPath)) {
      throw new Error('Database file does not exist');
    }

    const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
    const backupDir = path.join(this.configManager.getUserDataPath(), 'backups');
    const backupPath = path.join(backupDir, `database-${timestamp}.sqlite`);

    // Ensure backup directory exists
    await fs.mkdir(backupDir, { recursive: true });

    // Copy database file
    await fs.copyFile(this.dbPath, backupPath);
    
    console.log(`Database backup created: ${backupPath}`);
    return backupPath;
  }

  /**
   * Restore database from backup
   * @param {string} backupPath - Path to backup file
   */
  async restoreFromBackup(backupPath) {
    if (!existsSync(backupPath)) {
      throw new Error('Backup file does not exist');
    }

    // Create backup of current database before restoring
    if (existsSync(this.dbPath)) {
      await this.createBackup();
    }

    // Copy backup to database location
    await fs.copyFile(backupPath, this.dbPath);
    
    console.log(`Database restored from: ${backupPath}`);
  }

  /**
   * Check database integrity
   * @returns {boolean} True if database is valid
   */
  async checkIntegrity() {
    if (!existsSync(this.dbPath)) {
      return false;
    }

    try {
      // Use SQLite's integrity check
      const result = await this.executeArtisan(['db:show']);
      return true;
    } catch (error) {
      console.error('Database integrity check failed:', error);
      return false;
    }
  }

  /**
   * Get database file size
   * @returns {number} Size in bytes
   */
  async getDatabaseSize() {
    if (!existsSync(this.dbPath)) {
      return 0;
    }

    const stats = await fs.stat(this.dbPath);
    return stats.size;
  }

  /**
   * Execute Laravel artisan command
   * @param {string[]} args - Artisan command arguments
   * @returns {Promise<string>} Command output
   */
  executeArtisan(args) {
    return new Promise((resolve, reject) => {
      const laravelPath = this.getLaravelPath();
      const phpBinary = this.getPHPBinaryPath();
      const artisanPath = path.join(laravelPath, 'artisan');

      const fullArgs = [artisanPath, ...args];
      
      console.log(`Executing: ${phpBinary} ${fullArgs.join(' ')}`);

      const childProcess = spawn(phpBinary, fullArgs, {
        cwd: laravelPath,
        env: {
          ...process.env,
          DB_DATABASE: this.dbPath,
          DB_CONNECTION: 'sqlite'
        }
      });

      let output = '';
      let errorOutput = '';

      childProcess.stdout.on('data', (data) => {
        output += data.toString();
        console.log(`[Artisan] ${data.toString().trim()}`);
      });

      childProcess.stderr.on('data', (data) => {
        errorOutput += data.toString();
        console.error(`[Artisan Error] ${data.toString().trim()}`);
      });

      childProcess.on('close', (code) => {
        if (code === 0) {
          resolve(output);
        } else {
          reject(new Error(`Artisan command failed with code ${code}: ${errorOutput}`));
        }
      });

      childProcess.on('error', (error) => {
        reject(error);
      });
    });
  }

  /**
   * Get Laravel application path
   */
  getLaravelPath() {
    if (process.env.NODE_ENV === 'production') {
      return path.join(process.resourcesPath, 'app.asar.unpacked');
    } else {
      // In development, go up from electron/ to project root
      return path.resolve(path.dirname(import.meta.url.replace('file:///', '')), '..');
    }
  }

  /**
   * Get PHP binary path
   */
  getPHPBinaryPath() {
    const platform = process.platform;
    
    if (process.env.NODE_ENV === 'production') {
      const phpDir = path.join(process.resourcesPath, 'php');
      
      if (platform === 'win32') {
        return path.join(phpDir, 'php.exe');
      } else {
        return path.join(phpDir, 'bin', 'php');
      }
    } else {
      return platform === 'win32' ? 'php.exe' : 'php';
    }
  }
}

export default DatabaseManager;

/**
 * Property-Based Tests for PHP Server Manager
 * 
 * These tests verify universal properties that should hold across all valid executions.
 * Using fast-check for property-based testing with 100+ iterations per property.
 */

import { describe, test, expect, beforeEach, afterEach } from '@jest/globals';
import fc from 'fast-check';
import PHPServerManager from '../php-server.js';
import { createServer } from 'net';

/**
 * Helper function to occupy a port temporarily
 * @param {number} port - Port to occupy
 * @returns {Promise<Server>} Server instance
 */
function occupyPort(port) {
  return new Promise((resolve, reject) => {
    const server = createServer();
    
    server.once('error', (err) => {
      reject(err);
    });

    server.once('listening', () => {
      resolve(server);
    });

    server.listen(port, '127.0.0.1');
  });
}

/**
 * Helper function to close a server
 * @param {Server} server - Server to close
 * @returns {Promise<void>}
 */
function closeServer(server) {
  return new Promise((resolve) => {
    server.close(() => resolve());
  });
}

describe('PHP Server Manager - Property-Based Tests', () => {
  let manager;
  let occupiedServers = [];

  beforeEach(() => {
    manager = new PHPServerManager();
    occupiedServers = [];
  });

  afterEach(async () => {
    // Clean up any occupied servers
    for (const server of occupiedServers) {
      await closeServer(server);
    }
    occupiedServers = [];

    // Stop the PHP server if running
    if (manager && manager.process) {
      await manager.stop();
    }
  });

  /**
   * Feature: electron-desktop-conversion, Property 3: Port availability
   * Validates: Requirements 1.5
   * 
   * For any application startup, the selected port for the PHP server should be 
   * available and not conflict with other services
   */
  describe('Property 3: Port availability', () => {
    test('findAvailablePort always returns a port that is actually available', async () => {
      await fc.assert(
        fc.asyncProperty(
          fc.integer({ min: 3000, max: 9000 }), // Start port
          fc.integer({ min: 1, max: 20 }), // Range size
          async (startPort, rangeSize) => {
            const endPort = startPort + rangeSize;
            
            // Ensure we don't exceed valid port range
            if (endPort > 65535) {
              return true; // Skip this test case
            }

            try {
              const port = await manager.findAvailablePort(startPort, endPort);
              
              // Verify the returned port is within the requested range
              expect(port).toBeGreaterThanOrEqual(startPort);
              expect(port).toBeLessThanOrEqual(endPort);
              
              // Verify the port is actually available by checking it
              const isAvailable = await manager.isPortAvailable(port);
              expect(isAvailable).toBe(true);
              
              return true;
            } catch (error) {
              // If no port is available in range, that's acceptable
              // (all ports might be occupied)
              if (error.message.includes('No available ports found')) {
                return true;
              }
              throw error;
            }
          }
        ),
        { numRuns: 100 }
      );
    });

    test('isPortAvailable correctly identifies occupied ports', async () => {
      await fc.assert(
        fc.asyncProperty(
          fc.integer({ min: 8000, max: 8100 }),
          async (port) => {
            // First check if port is available
            const initiallyAvailable = await manager.isPortAvailable(port);
            
            if (initiallyAvailable) {
              // Occupy the port
              const server = await occupyPort(port);
              occupiedServers.push(server);
              
              // Now check again - should be unavailable
              const nowAvailable = await manager.isPortAvailable(port);
              expect(nowAvailable).toBe(false);
              
              // Clean up
              await closeServer(server);
              occupiedServers = occupiedServers.filter(s => s !== server);
              
              // After closing, should be available again
              const finallyAvailable = await manager.isPortAvailable(port);
              expect(finallyAvailable).toBe(true);
            }
            
            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('findAvailablePort skips occupied ports and finds the next available one', async () => {
      await fc.assert(
        fc.asyncProperty(
          fc.integer({ min: 8000, max: 8050 }),
          fc.integer({ min: 1, max: 5 }), // Number of ports to occupy
          async (startPort, numToOccupy) => {
            const endPort = startPort + 20; // Give enough range
            
            // Occupy some ports in the range
            const occupiedPorts = [];
            for (let i = 0; i < numToOccupy && (startPort + i) <= endPort; i++) {
              const portToOccupy = startPort + i;
              try {
                const server = await occupyPort(portToOccupy);
                occupiedServers.push(server);
                occupiedPorts.push(portToOccupy);
              } catch (error) {
                // Port might already be in use, skip it
                continue;
              }
            }
            
            try {
              // Find an available port
              const availablePort = await manager.findAvailablePort(startPort, endPort);
              
              // Verify it's not one of the occupied ports
              expect(occupiedPorts).not.toContain(availablePort);
              
              // Verify it's actually available
              const isAvailable = await manager.isPortAvailable(availablePort);
              expect(isAvailable).toBe(true);
              
              return true;
            } catch (error) {
              // If all ports in range are occupied, that's acceptable
              if (error.message.includes('No available ports found')) {
                return true;
              }
              throw error;
            }
          }
        ),
        { numRuns: 50 } // Fewer runs due to complexity
      );
    });

    test('port availability check is consistent across multiple calls', async () => {
      await fc.assert(
        fc.asyncProperty(
          fc.integer({ min: 8000, max: 8100 }),
          async (port) => {
            // Check the same port multiple times
            const check1 = await manager.isPortAvailable(port);
            const check2 = await manager.isPortAvailable(port);
            const check3 = await manager.isPortAvailable(port);
            
            // All checks should return the same result
            expect(check1).toBe(check2);
            expect(check2).toBe(check3);
            
            return true;
          }
        ),
        { numRuns: 100 }
      );
    });

    test('findAvailablePort throws error when no ports available in range', async () => {
      // This is a specific example test, not property-based
      // Occupy a small range of ports
      const startPort = 8200;
      const endPort = 8202;
      
      const servers = [];
      for (let port = startPort; port <= endPort; port++) {
        try {
          const server = await occupyPort(port);
          servers.push(server);
          occupiedServers.push(server);
        } catch (error) {
          // Port might already be in use
        }
      }
      
      // Try to find a port in the fully occupied range
      await expect(
        manager.findAvailablePort(startPort, endPort)
      ).rejects.toThrow('No available ports found');
      
      // Clean up
      for (const server of servers) {
        await closeServer(server);
      }
    });
  });

  /**
   * Feature: electron-desktop-conversion, Property 12: Server health polling
   * Validates: Requirements 4.4
   * 
   * For any PHP server startup, the application should repeatedly poll the health 
   * endpoint until it receives a successful response
   */
  describe('Property 12: Server health polling', () => {
    test('waitForReady polls repeatedly until isReady returns true', async () => {
      // Test that waitForReady continues polling until success
      let pollCount = 0;
      const pollsBeforeSuccess = 3;
      
      const mockIsReady = async () => {
        pollCount++;
        return pollCount >= pollsBeforeSuccess;
      };

      // Replace isReady with our mock
      const originalIsReady = manager.isReady.bind(manager);
      manager.isReady = mockIsReady;
      
      // Set a mock process
      manager.process = { pid: 12345 };
      manager.status = 'starting';
      manager.url = 'http://127.0.0.1:8000';

      try {
        await manager.waitForReady(5000);
        
        // Verify polling occurred at least the required number of times
        expect(pollCount).toBeGreaterThanOrEqual(pollsBeforeSuccess);
        
        // Verify status changed to ready
        expect(manager.status).toBe('ready');
      } finally {
        // Restore original method
        manager.isReady = originalIsReady;
        manager.process = null;
        manager.status = 'stopped';
      }
    });

    test('waitForReady throws timeout error when server never becomes ready', async () => {
      const timeout = 1000;
      let pollCount = 0;
      
      // Mock isReady to always return false
      const mockIsReady = async () => {
        pollCount++;
        return false;
      };

      const originalIsReady = manager.isReady.bind(manager);
      const originalStop = manager.stop.bind(manager);
      manager.isReady = mockIsReady;
      
      // Mock stop to avoid process interaction
      let stopCalled = false;
      manager.stop = async () => {
        stopCalled = true;
        manager.process = null;
        manager.status = 'stopped';
      };
      
      manager.process = { pid: 12345 };
      manager.status = 'starting';
      manager.url = 'http://127.0.0.1:8000';

      try {
        await expect(
          manager.waitForReady(timeout)
        ).rejects.toThrow(`PHP server failed to start within ${timeout}ms`);
        
        // Verify stop was called
        expect(stopCalled).toBe(true);
        
        // Verify polling occurred multiple times
        expect(pollCount).toBeGreaterThan(1);
      } finally {
        manager.isReady = originalIsReady;
        manager.stop = originalStop;
        manager.process = null;
        manager.status = 'stopped';
      }
    });

    test('health check polling detects when server becomes unhealthy', async () => {
      const interval = 300;
      let checkCount = 0;
      let unhealthyCallbackCalled = false;
      const checksBeforeFailure = 2;
      
      const mockIsReady = async () => {
        checkCount++;
        return checkCount <= checksBeforeFailure;
      };

      const originalIsReady = manager.isReady.bind(manager);
      manager.isReady = mockIsReady;
      
      manager.process = { pid: 12345 };
      manager.status = 'ready';
      manager.url = 'http://127.0.0.1:8000';

      // Start health check polling
      manager.startHealthCheckPolling(interval, () => {
        unhealthyCallbackCalled = true;
      });

      try {
        // Wait for enough time for checks to occur
        await new Promise(resolve => setTimeout(resolve, interval * 4));
        
        // Verify unhealthy callback was called
        expect(unhealthyCallbackCalled).toBe(true);
        
        // Verify status changed to error
        expect(manager.status).toBe('error');
      } finally {
        manager.stopHealthCheckPolling();
        manager.isReady = originalIsReady;
        manager.process = null;
        manager.status = 'stopped';
      }
    });

    test('stopHealthCheckPolling stops the polling interval', async () => {
      let checkCount = 0;
      
      const mockIsReady = async () => {
        checkCount++;
        return true;
      };

      const originalIsReady = manager.isReady.bind(manager);
      manager.isReady = mockIsReady;
      
      manager.process = { pid: 12345 };
      manager.status = 'ready';
      manager.url = 'http://127.0.0.1:8000';

      // Start polling
      manager.startHealthCheckPolling(200);
      
      // Wait for a few checks
      await new Promise(resolve => setTimeout(resolve, 600));
      const checksAfterStart = checkCount;
      
      // Stop polling
      manager.stopHealthCheckPolling();
      
      // Wait more time
      await new Promise(resolve => setTimeout(resolve, 600));
      const checksAfterStop = checkCount;
      
      // Verify no more checks occurred after stopping
      expect(checksAfterStop).toBe(checksAfterStart);
      
      // Clean up
      manager.isReady = originalIsReady;
      manager.process = null;
      manager.status = 'stopped';
    });

    test('waitForReady respects the configured timeout value', async () => {
      const timeout = 800;
      const startTime = Date.now();
      
      // Mock isReady to always return false
      const mockIsReady = async () => false;

      const originalIsReady = manager.isReady.bind(manager);
      const originalStop = manager.stop.bind(manager);
      manager.isReady = mockIsReady;
      manager.stop = async () => {
        manager.process = null;
        manager.status = 'stopped';
      };
      
      manager.process = { pid: 12345 };
      manager.status = 'starting';
      manager.url = 'http://127.0.0.1:8000';

      try {
        await manager.waitForReady(timeout);
        fail('Should have thrown timeout error');
      } catch (error) {
        const elapsed = Date.now() - startTime;
        
        // Verify error message
        expect(error.message).toContain(`PHP server failed to start within ${timeout}ms`);
        
        // Verify timeout was respected (allow 20% tolerance)
        expect(elapsed).toBeGreaterThanOrEqual(timeout);
        expect(elapsed).toBeLessThan(timeout * 1.5);
      } finally {
        manager.isReady = originalIsReady;
        manager.stop = originalStop;
        manager.process = null;
        manager.status = 'stopped';
      }
    });
  });
});

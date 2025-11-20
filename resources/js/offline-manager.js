// Offline Data Manager using IndexedDB
class OfflineManager {
  constructor() {
    this.dbName = 'EquiserveDB';
    this.dbVersion = 1;
    this.db = null;
  }

  async init() {
    return new Promise((resolve, reject) => {
      const request = indexedDB.open(this.dbName, this.dbVersion);

      request.onerror = () => reject(request.error);
      request.onsuccess = () => {
        this.db = request.result;
        resolve(this.db);
      };

      request.onupgradeneeded = (event) => {
        const db = event.target.result;

        // Create object stores for offline data
        if (!db.objectStoreNames.contains('products')) {
          db.createObjectStore('products', { keyPath: 'id' });
        }
        if (!db.objectStoreNames.contains('customers')) {
          db.createObjectStore('customers', { keyPath: 'id' });
        }
        if (!db.objectStoreNames.contains('sales')) {
          db.createObjectStore('sales', { keyPath: 'id', autoIncrement: true });
        }
        if (!db.objectStoreNames.contains('pending-sales')) {
          db.createObjectStore('pending-sales', { keyPath: 'id', autoIncrement: true });
        }
        if (!db.objectStoreNames.contains('dashboard-cache')) {
          db.createObjectStore('dashboard-cache', { keyPath: 'key' });
        }
      };
    });
  }

  async saveData(storeName, data) {
    const tx = this.db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    
    if (Array.isArray(data)) {
      for (const item of data) {
        await store.put(item);
      }
    } else {
      await store.put(data);
    }
    
    return tx.complete;
  }

  async getData(storeName, key = null) {
    const tx = this.db.transaction(storeName, 'readonly');
    const store = tx.objectStore(storeName);
    
    if (key) {
      return store.get(key);
    }
    return store.getAll();
  }

  async clearStore(storeName) {
    const tx = this.db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    return store.clear();
  }

  async savePendingSale(saleData) {
    const tx = this.db.transaction('pending-sales', 'readwrite');
    const store = tx.objectStore('pending-sales');
    return store.add({
      data: saleData,
      timestamp: Date.now(),
      synced: false
    });
  }

  async getPendingSales() {
    return this.getData('pending-sales');
  }

  async cacheDashboardData(data) {
    return this.saveData('dashboard-cache', {
      key: 'dashboard',
      data: data,
      timestamp: Date.now()
    });
  }

  async getCachedDashboardData() {
    const cached = await this.getData('dashboard-cache', 'dashboard');
    if (cached && (Date.now() - cached.timestamp < 3600000)) { // 1 hour
      return cached.data;
    }
    return null;
  }
}

// Export singleton instance
window.offlineManager = new OfflineManager();

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
  try {
    await window.offlineManager.init();
    console.log('Offline manager initialized');
    
    // Trigger background sync when coming online
    window.addEventListener('online', () => {
      if ('serviceWorker' in navigator && 'sync' in registration) {
        navigator.serviceWorker.ready.then((reg) => {
          return reg.sync.register('sync-sales');
        });
      }
    });
  } catch (err) {
    console.error('Failed to initialize offline manager:', err);
  }
});

// Export for use in other scripts
export default window.offlineManager;

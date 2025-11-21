# PHP Server Manager

Manages the embedded PHP development server lifecycle for the Laravel application.

## Features

- Automatic port finding to avoid conflicts
- PHP process spawning with Laravel-specific configuration
- Health check polling with configurable timeout
- Graceful server shutdown and cleanup
- Platform-specific PHP binary path resolution

## Usage

```javascript
import PHPServerManager from './php-server.js';

const manager = new PHPServerManager();

// Start server (auto-finds available port)
const url = await manager.start();
console.log(`Server running at: ${url}`);

// Check if server is ready
const ready = await manager.isReady();

// Stop server gracefully
await manager.stop();
```

## API

See inline JSDoc comments in `php-server.js` for detailed API documentation.

## Testing

Run manual tests: `node electron/test-php-server.js`

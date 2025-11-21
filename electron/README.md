# Electron Application Layer

This directory contains the Electron application code that wraps the Laravel application.

## File Structure

- `main.js` - Main process entry point and application lifecycle management
- `preload.js` - Preload script for secure IPC communication
- `php-server.js` - PHP server process management
- `window-manager.js` - Window creation and management
- `database-manager.js` - Database initialization and migrations
- `update-manager.js` - Auto-update functionality
- `config-manager.js` - Configuration and environment management
- `error-handler.js` - Centralized error handling
- `utils/` - Utility functions and helpers

## Architecture

The Electron layer is responsible for:
1. Starting and managing the embedded PHP server
2. Creating and managing application windows
3. Handling native desktop integration (tray, shortcuts, etc.)
4. Managing database initialization and migrations
5. Handling auto-updates
6. Error handling and logging

The Laravel application remains completely unchanged and communicates with Electron via HTTP on localhost.

## Development

Run in development mode:
```bash
npm run electron:dev
```

Run Electron only (requires Laravel server running separately):
```bash
npm run electron:start
```

## Building

Build for all platforms:
```bash
npm run electron:build
```

Build for specific platform:
```bash
npm run electron:build:win
npm run electron:build:mac
npm run electron:build:linux
```

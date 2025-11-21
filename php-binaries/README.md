# PHP Binaries Directory

This directory contains platform-specific PHP binaries that will be bundled with the Electron application.

## Directory Structure

- `win/` - Windows PHP binaries
- `mac/` - macOS PHP binaries
- `linux/` - Linux PHP binaries

## Required PHP Extensions

Each platform's PHP binary must include the following extensions:
- SQLite3 (required for database)
- OpenSSL (required for Laravel)
- mbstring (required for Laravel)
- tokenizer (required for Laravel)
- XML (required for Laravel)
- ctype (required for Laravel)
- JSON (required for Laravel)
- fileinfo (required for file operations)
- PDO (required for database)

## Obtaining PHP Binaries

### Windows
Download from: https://windows.php.net/download/
- Choose "Thread Safe" version
- Extract to `win/` directory

### macOS
Use Homebrew or download from: https://www.php.net/downloads
- Compile with static linking if possible
- Extract to `mac/` directory

### Linux
Download static builds or compile from source
- Extract to `linux/` directory

## Testing PHP Binaries

After placing binaries, test with:
```bash
# Windows
php-binaries\win\php.exe -v

# macOS/Linux
php-binaries/mac/php -v
php-binaries/linux/php -v
```

## File Structure Example

```
php-binaries/
├── win/
│   ├── php.exe
│   ├── php.ini
│   └── ext/
│       ├── php_sqlite3.dll
│       ├── php_openssl.dll
│       └── ...
├── mac/
│   ├── php
│   ├── php.ini
│   └── lib/
│       └── php/
│           └── extensions/
├── linux/
│   ├── php
│   ├── php.ini
│   └── lib/
│       └── php/
│           └── extensions/
```

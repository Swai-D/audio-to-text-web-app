# ZIP Extension Setup for DOCX Export

## Problem
DOCX export requires PHP ZIP extension which is not enabled by default in XAMPP.

## Solution

### Step 1: Enable ZIP Extension
1. Open `C:\xampp\php\php.ini` in Notepad or any text editor
2. Find the line: `;extension=zip`
3. Remove the semicolon (;) to uncomment it: `extension=zip`
4. Save the file

### Step 2: Restart Apache
1. Open XAMPP Control Panel
2. Stop Apache
3. Start Apache again

### Step 3: Verify Installation
Run this command to check if ZIP extension is loaded:
```bash
php -m | findstr zip
```

You should see `zip` in the output.

### Alternative: Manual Installation
If the above doesn't work:

1. Download `php_zip.dll` for your PHP version from https://pecl.php.net/package/zip
2. Place it in `C:\xampp\php\ext\`
3. Add `extension=zip` to php.ini
4. Restart Apache

## Current Status
- ✅ PhpWord library installed
- ❌ ZIP extension needs to be enabled
- ✅ DOCX export code ready

## Test
After enabling ZIP extension, try downloading a DOCX file from the app.

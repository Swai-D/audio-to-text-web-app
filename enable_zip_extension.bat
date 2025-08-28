@echo off
echo Enabling ZIP extension for DOCX export...
echo.

REM Check if php.ini exists
if not exist "C:\xampp\php\php.ini" (
    echo ERROR: php.ini not found at C:\xampp\php\php.ini
    echo Please make sure XAMPP is installed correctly.
    pause
    exit /b 1
)

REM Create backup
echo Creating backup of php.ini...
copy "C:\xampp\php\php.ini" "C:\xampp\php\php.ini.backup"

REM Enable ZIP extension
echo Enabling ZIP extension...
powershell -Command "(Get-Content 'C:\xampp\php\php.ini') -replace ';extension=zip', 'extension=zip' | Set-Content 'C:\xampp\php\php.ini'"

echo.
echo ZIP extension enabled successfully!
echo.
echo Please restart Apache in XAMPP Control Panel for changes to take effect.
echo.
echo After restarting Apache, you can verify by running: php -m | findstr zip
echo.
pause

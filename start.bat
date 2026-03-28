@echo off
setlocal
where php >nul 2>&1
if errorlevel 1 (
  echo PHP not found in PATH. Please install PHP 8 and enable pdo_sqlite.
  pause
  exit /b 1
)
echo Starting PHP dev server at http://localhost:8000
php -S localhost:8000 -t "%~dp0"
exit /b 0
@echo off
echo Starting Personal Improvement Devotional...
echo.

REM Check if PHP is available
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: PHP is not installed or not in PATH
    echo Please install PHP 8+ with PDO SQLite extension
    pause
    exit /b 1
)

REM Check if SQLite database exists, create if not
if not exist "personal_planner.db" (
    echo Creating SQLite database...
    echo.
    echo Note: The application will automatically initialize the database 
    echo on first access. Simply run the application and it will create 
    echo the necessary tables.
    echo.
) else (
    echo Database already exists.
)

REM Run the built-in PHP server
echo Starting PHP development server on http://localhost:8000
echo Press Ctrl+C to stop the server
echo.

php -S localhost:8000

echo.
echo Server stopped.
pause
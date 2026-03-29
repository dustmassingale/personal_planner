@echo off
REM Start PHP built-in server for Personal Planner
set PORT=8000
set HOST=localhost
set ROOT=%~dp0
cd /d %ROOT%
php -S %HOST%:%PORT% -t .

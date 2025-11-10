@echo off
REM This script starts the Laravel development server and the frontend development server concurrently.
REM It assumes you are running it from the root directory of your Laravel project.

echo Starting Laravel development server (php artisan serve)...
start /B php artisan serve

echo Starting frontend development server (npm run dev)...
npm run dev

REM The 'npm run dev' command will typically run indefinitely and keep this window open.
REM To stop, press Ctrl+C in this window to stop 'npm run dev'.
REM You may need to manually close the 'php artisan serve' process if it doesn't stop automatically.

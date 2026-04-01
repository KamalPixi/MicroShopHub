Production Docker setup

Services included:
- app: Laravel + PHP-FPM
- nginx: public web server
- db: MySQL 8
- phpmyadmin: database admin UI
- queue: Laravel queue worker
- scheduler: Laravel scheduler worker
- setup: one-time dependency and asset build helper

First deploy flow:
1. Make sure the project files are on the server.
2. Optionally export the MySQL Docker variables from `docker/.env.example` if you want to override the defaults.
3. Run the setup helper once to install PHP dependencies and build frontend assets.
4. Start the stack.
5. Open the installer in the browser and complete setup.

Notes:
- The installer writes back to the mounted `.env` file on the server.
- Queue and scheduler are separate containers so jobs and scheduled tasks keep running.
- phpMyAdmin is exposed on port 8080 by default.
- Default MySQL values come from the Docker env sample and can be changed there before first start.

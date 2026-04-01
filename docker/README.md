Production Docker setup

Services included:
- app: Laravel + PHP-FPM
- nginx: public web server
- db: MySQL 8
- phpmyadmin: database admin UI
- queue: Laravel queue worker
- scheduler: Laravel scheduler worker
- setup: one-time dependency and asset build helper

If you are deploying with containerd/nerdctl, use `docker-compose.nerdctl.yml` instead of the production compose file.
For nerdctl, use `docker/nerdctl-start.sh` so db starts first, setup runs once, and the rest of the services come up in order.

PHP requirement:
- The Docker image is now PHP 8.4 to match the current Composer platform requirements.

Frontend assets:
- Tailwind is now loaded from the committed Vite build instead of the CDN.
- Build the frontend locally once, commit `public/build`, and the server will not need npm or Vite.
- The Docker setup helper now only installs PHP dependencies.

Startup behavior:
- The PHP image now waits for the database itself before starting Laravel.
- This avoids nerdctl Compose dependency warnings and keeps startup predictable.

First deploy flow:
1. Make sure the project files are on the server.
2. Optionally export the MySQL Docker variables from `docker/.env.example` if you want to override the defaults.
3. Run the nerdctl start helper or the setup helper once to install PHP dependencies, generate the app key if needed, and bring the services up in order.
4. Open the installer in the browser and complete setup.

Notes:
- The installer writes back to the mounted `.env` file on the server.
- Queue and scheduler are separate containers so jobs and scheduled tasks keep running.
- phpMyAdmin is exposed on port 8080 by default.
- Default MySQL values come from the Docker env sample and can be changed there before first start.

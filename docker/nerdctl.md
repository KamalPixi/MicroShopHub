nerdctl deployment

Use `docker-compose.nerdctl.yml` for containerd/nerdctl environments.

Recommended flow:
1. Start MySQL and phpMyAdmin with the compose file.
2. Run the `setup` service once to install PHP dependencies and build assets.
3. Start the full stack.
4. Open the installer and finish setup.

Notes:
- This variant avoids Docker Compose-specific dependency conditions and profiles.
- It keeps the services simple for containerd-based environments.
- If your server uses a custom `.env`, update the Docker MySQL variables before first start.

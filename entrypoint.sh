#!/bin/bash
# Check if .env file exists; if not, generate one using runtime environment variables
if [ ! -f /var/www/html/.env ]; then
  echo "APP_KEY=${APP_KEY}" > /var/www/html/.env
  echo "DB_HOST=${DB_HOST}" >> /var/www/html/.env
  # Append any other required variables
fi

# Execute the main command (e.g. start Apache)
exec "$@"

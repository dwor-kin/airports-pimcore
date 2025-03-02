#!/bin/bash
set -e

apt-get update && apt-get install -y cron

CRON_JOB="*/15 * * * * root /usr/bin/php /var/www/html/bin/console command:import-todos >> /var/www/html/var/logs/import.log 2>&1"

(crontab -l 2>/dev/null | grep -F "$CRON_JOB") || (echo "$CRON_JOB" >> /etc/crontab)

service cron start

exec "$@"
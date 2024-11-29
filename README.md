# Using Docker

docker compose exec appserver /app/vendor/bin/drush cr



zcat ./.lando/database/dump.sql.gz | docker exec -i 33366c271b1c /usr/bin/mysql -u root --password=root drupal10

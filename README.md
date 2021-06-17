# Symfony Test Project

Simple Project and Task control system with API exposure using Symfony 5, Doctrine ORM, PHP8 and PostgreSQL 13.

What is missing:
* API and Web Authentication
* .htaccess file
* Proper UI

Requirements:
* Installed PHP8 or
```bash
docker pull php
```
* Configured PostgreSQL Server - see .env file for reference
```bash
docker pull postgres
```
* Use PG dump file if needed (sf_project.dump) or run
```bash
php bin/console doctrine:migrations:migrate
```

API testing Postman collection included (SF Project.postman_collection.json).

UUID could be used instead of integer IDs, which makes things cleaner from API perspective.

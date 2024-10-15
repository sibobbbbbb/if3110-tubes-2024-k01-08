# Tugas Besar IF3110 2024/2025

## How To Run

1. Create network

   ```bash
   make network
   ```

   or

   ```bash
   docker network create linkinpurry-network
   ```

2. Database

   ```bash
   make db
   ```

   or

   ```bash
   docker compose up --build db
   ```

3. Website

   - For development,

     ```bash
     make web-dev
     ```

     or

     ```bash
     docker compose up --build web-dev
     ```

   - For production

     ```
     make web-prod
     ```

     or

     ```bash
     docker compose up --build web-prod
     ```

4. Hard reset (delete database data)

   ```bash
   make reset
   ```

   or

   ```bash
   docker compose down
   sudo rm -rf ./db-data
   ```

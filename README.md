## Prerequisites

- [Docker](https://www.docker.com/) 
- [Make](https://www.gnu.org/software/make/)

## Getting Started

To get the project up and running for the first time, follow these steps:

1. **Start the containers:**
   ```bash
   make up
   ```

2. **Install dependencies:**
   ```bash
   make install
   ```

3. **Run database migrations:**
   ```bash
   make migrate
   ```

4. **Load mock data (optional):**
   ```bash
   make init
   ```
   
5. **Start the server:**
   ```bash
   make start
   ```

6. **Clean the docker containers:**
   ```bash
   make clean
   ```

The API will be available at `http://localhost:8000`.
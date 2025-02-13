# Stoyan-Milev-employees

# 🚀 Getting Started with the Project

## 📌 Prerequisites
Before you begin, ensure you have the following installed on your machine:
- **Docker** ([Download Docker](https://docs.docker.com/get-docker/))
- **Docker Compose** ([Install Docker Compose](https://docs.docker.com/compose/install/))

## 🔧 Setup & Run the Project

### Step 1: Start the Docker Containers
Open a terminal, navigate to the project directory, and run:

```sh
docker-compose up -d
```

This command will:
✅ Download and set up the necessary Docker images  
✅ Start all required services in the background (`-d` means "detached mode")  

### Step 2: Wait for Services to Be Ready
Give Docker a moment to initialize the containers. You can check the status by running:

```sh
docker ps
```

Once all services are up and running, proceed to the next step.

### Step 3: Access the Application
Once the setup is complete, open your browser and go to:

👉 [http://localhost:8080/](http://localhost:8080/)

This will load the main interface of the project.

### Step 4: Use the Console (Optional)
To access the application's command-line tools, open a terminal and run:

```sh
docker-compose exec app bash
```

You can then run various commands to interact with the project.

## 🛑 Stopping the Project
If you need to stop the running containers, simply run:

```sh
docker-compose down
```

This will shut down and remove all containers but keep your data intact.

## 🛠 Troubleshooting
- If you encounter permission issues, try running commands with `sudo`.
- To restart the project, use:

  ```sh
  docker-compose down && docker-compose up -d
  ```

For further assistance, check the logs with:

```sh
docker-compose logs -f
```

---

Now you're ready to start working with the project! 🚀


# Task Manager Application

## Overview

This is a PHP-based task manager application that allows users to manage tasks and users with CRUD functionality.

## Getting Started

### Prerequisites

- Docker
- Docker Compose

### Running the Application

1. Clone the repository:

    ```bash
    git clone https://github.com/yourusername/task_manager.git
    cd task_manager/docker
    ```

2. Build and run the application:

    ```bash
    docker-compose up --build
    ```

3. Access the application at `http://localhost`.

### Database Setup

- The application uses MySQL as the database.
- The database will be created automatically by Docker.

### Features

- CRUD operations for Users and Tasks
- Pagination and sorting
- Filtering tasks by User
- Marking tasks as complete

## Author

Your Name

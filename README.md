# Musement Weather Application
Backend PHP tech homework

This application allows you to get weather information for all or specific cities Musement

# Getting Started

The application is configured to run inside docker containers. Make sure docker is installed on your system

<details>
<summary>Install dependencies</summary>

Docker install:
```
sudo apt update \
    && sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg-agent \
    software-properties-common \
    && curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add - \
    && sudo add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" \
    && sudo apt update \
    && sudo apt --yes --no-install-recommends install docker-ce \
    && sudo usermod --append --groups docker "$USER" \
    && sudo systemctl enable docker \
    && printf '\nDocker installed successfully\n\n'
```

Docker-Compose install:
```
sudo curl -L "https://github.com/docker/compose/releases/download/1.26.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Optional symlink
sudo ln -s /usr/local/bin/docker-compose /usr/bin/docker-compose
```
</details>

## Start project

**Simple way**

You can start the project by one command
```
docker-compose up -d
```

**Setup setting and start**

You can change PHP and Nginx setting for your environment. Just update or create ./docker/dev/env/php/php.ini and build and start containers:

```
./docker/start.sh
```

## Synchronization of Musement cities

By default, there is no information about cities or countries in the application (unless you run fixtures through by `bin/console doctrine:fixtures:load` command), to get a full list of cities, run the command:
```
bin/console app:musement-city-synchronization
```

The command arguments:
```
--dry-run: Execute the synchronization as a dry run (without saving to the database)
-v: Verbose mode
-vv: Very verbose mode
```

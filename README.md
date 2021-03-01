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

# Endpoints

There are the following API endpoint:

### Get cities

**URL:** /api/v3/cities/

**HTTP Method:** GET

**Content-Type:** application/json

**Example Request:**
```
curl --request 'GET' 'http://localhost:8085/api/v3/cities/' -i
```
### Response Codes:

#### 200
**Returned When:** Valid parameters were in request and the query to the database did not fail

**Response Content Type:** application/json

**Response Body:**
```
[
    {
        id: 401,
        name: "Amsterdam",
        code: "amsterdam",
        sourceId: 57,
        latitude: 52.374,
        longitude: 4.9,
        createdAt: "2021-02-28T17:54:23+00:00",
        updatedAt: "2021-02-28T17:54:23+00:00"
    },
    {
        id: 402,
        name: "Paris",
        code: "paris",
        sourceId: 40,
        latitude: 48.866,
        longitude: 2.355,
        createdAt: "2021-02-28T17:54:23+00:00",
        updatedAt: "2021-02-28T17:54:23+00:00"
    },
    ...
}
```

#### 500
**Returned When:** Cities information from database fails

**Response Content Type:**
none (empty response)

***


### Get specific city

**URL:** /api/v3/cities/[city]

**HTTP Method:** GET

**Content-Type:** application/json

**Example Request:**
```
curl --request 'GET' 'http://localhost:8085/api/v3/cities/401' -i
```
### Response Codes:

#### 200
**Returned When:** Valid parameters were in request and the query to the database did not fail

**Response Content Type:** application/json

**Response Body:**
```
{
    id: 401,
    name: "Amsterdam",
    code: "amsterdam",
    sourceId: 57,
    latitude: 52.374,
    longitude: 4.9,
    createdAt: "2021-02-28T17:54:23+00:00",
    updatedAt: "2021-02-28T17:54:23+00:00"
}
```

#### 404
**Returned When:** City with [city] not found

#### 500
**Returned When:** City information from database fails

**Response Content Type:**
none (empty response)

***

### Get forecast

**URL:** /api/v3/forecast/

**HTTP Method:** GET

**Content-Type:** application/json

**Required Input:**
```
days - integer.
```

**Optional Input:**
```
city - integer. City id
```

**Example Request:**
```
curl --request 'GET' 'http://localhost:8085/api/v3/forecast/' -i
```
### Response Codes:

#### 200
**Returned When:** Valid parameters were in request and the query to the database did not fail

**Response Content Type:** application/json

**Response Body:**
```
[
    {
        location: "Amsterdam",
        forecast: {
            "2021-03-01": "Partly cloudy",
            "2021-03-02": "Partly cloudy"
        }
    },
    ...
]
```

#### 400
**Returned When:** Invalid input

#### 500
**Returned When:** City information from database fails

**Response Body Format:**
```
{
    "errors": "Error message"
}
```

**Response Content Type:**
none (empty response)

***

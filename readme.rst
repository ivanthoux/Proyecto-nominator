## Framework

    CodeIgniter

## how to setup the project

### Need to install

    docker
    docker-compose

Init with the command:

` docker-compose up -d `

To edit this need to edit the file .env

### to connect with mysql with a client

host: localhost:3306 user: nominator pass: nominator

### If you're using docker, change in database.php config file hostname from 127.0.0.1 to "mysql"

### RUN MIGRATIONS: Do it inside docker php 
    cd nominator
    php index.php migrate --server_name=localhost
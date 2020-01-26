# Laravel API

The main web application running at TBD.

## Requirements

You'll need all of the following in order to run the project.

1. Preferably a [\*nix](https://en.wikipedia.org/wiki/Unix-like) operating system
2. [Docker](https://docs.docker.com/install)
3. [docker-compose](https://docs.docker.com/compose/install)
4. [PHP ^7.3](https://www.php.net/manual/en/install.php) (optional)
5. [Composer](https://getcomposer.org/download) (optional)
6. [node.js](https://nodejs.org/en/download) (optional)
7. [npm](https://www.npmjs.com/get-npm) (optional)

## Installation

Use `docker-compose` to build the `docker` images and create the local network.

```bash
docker-compose build
```

Use `docker-compose` to install the required `composer` dependencies.

```bash
docker-compose -f docker-compose.dev-build.yml up
```

Change directory and file permissions.

```bash
./fix-permissions.sh
```

Bring up the containers in detached mode.

```bash
docker-compose up -d
```

Running the migrations.

```bash
docker-compose exec php php artisan migrate
```

Running the database seeders.

```bash
docker-compose run php php artisan db:seed
```

## Usage

##### After the containers are up and running you can access the following URLs in your browser.

API

```bash
http://localhost
```

phpMyAdmin (credentials inside `docker-compose.yml`)

```bash
http://localhost:81
```

Redis Commander

```bash
http://localhost:83
```

MailHog UI

```bash
http://localhost:84
```

## Development

Running `composer`.

```bash
docker-compose run composer {command}
```

Running `phpcs` and `phpunit`.

```bash
docker-compose -f docker-compose.test.yml up
```

Accessing a `docker-compose` service's shell.

```bash
docker-compose run {service_name} sh
```

Running the `PHP` command.

```bash
docker-compose run php php {arguments}
```

Running `Artisan` commands.

```bash
docker-compose run php php artisan {command}
```

#### Notes

Running any `artisan make:` command will create the files under the `root:root` user, thus making them un-editable by non-root users. \
To overcome this you should run the commands with `-u {uid}` or `-u {uid}:{gid}`.

```bash
docker-compose run -u {uid}:{gid} php php artisan make:{type} {name}
```

To get your `uid` and `gid` on \*nix operating systems run the following command in the terminal.

```bash
id
```

Another way would be by running the command as usual, without the `-u` argument and after that running the following command.

```bash
sudo chown -R {uid}:{gid} .
```

Caveat: If you update webpack.mix.js, you have to restart the node service.

## Production

**Building for production should be done after a fresh `git clone`.**

Installing the dependencies and building the assets in production mode.

```bash
docker-compose -f docker-compose.prod-build.yml up
```

After that you can run the same command from the [installation](#installation) section to bring up the containers.

## Contributing

We haven't got a particular flow in place at the moment.

## License

No licensing at the moment as we haven't talked about this. :(

# Showcase of a LAMP application

This is an example application to showcase the LAMP stack.

Frontend and backend code is not separated.
Almost no logic on the frontend.
The state is all in the database. Even if it is transient.

## TODO

- High score board
- Error management (e.g. when some request parameters are not defined).
- Style UI
- Documentation

## How to use

Start the images with docker compose

    docker compose up

Put your php files in the `./approot` folder and access them from your browser.

For example the file `info.php` outputs information about the enviroment
(in the container) and you can access that through the link
<http://localhost/info.php> once the server is started.

## 3rd party

The migration [Dockerfile](migrations/Dockerfile) and
[script](migrations/migrate.sh) are adapted from
[this repository](https://github.com/mathew-hall/mysql_migration).

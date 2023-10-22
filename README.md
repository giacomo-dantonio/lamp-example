# Showcase of a LAMP application

This is an example application to showcase the LAMP stack.

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

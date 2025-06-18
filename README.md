# GFModules - Viewer Web

This is the web frontend containing a flow to find discover patient data and an address book to find organization information.

## Development setup

Requirements:

- [php(>=8.3.0)](https://www.php.net/manual/en/install.general.php)
- [composer(>=2.2)](https://getcomposer.org/download/)
- npm(>=10.8.2) + [node(>=20)](https://nodejs.org/en/download)

Run the following commands to run this application in docker using [```sail```](https://laravel.com/docs/12.x/sail).

```bash
make setup
make run
```

After this you can access the application at [http://localhost:8500/](http://localhost:8500).

## Run on docker

It's possible to do a standalone run of the application using docker. This docker container will have the laravel application running on an nginx webserver running on port 80.
Note that you would either set environment variables (see `.env.example`), or mount your `.env` during docker run.

Make sure you build the frontend assets locally first:

```bash
    # Build assets
    npm run build
    
    # Build docker image
    make container-build
    
    # Run container
    docker run -ti --rm -p 8500:80 \
        --mount type=bind,source=./.env,target=/var/www/html/.env \
        gfmodules-viewer-web:latest
```

After this you can access the application at [http://localhost:8500/](http://localhost:8500).

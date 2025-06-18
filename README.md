# GFModules - Viewer Web

This viewer application can be used to find patient data and also provides an address book to find organization information.

## Disclaimer

This project and all associated code serve solely as documentation
and demonstration purposes to illustrate potential system
communication patterns and architectures.

This codebase:

- Is NOT intended for production use
- Does NOT represent a final specification
- Should NOT be considered feature-complete or secure
- May contain errors, omissions, or oversimplified implementations
- Has NOT been tested or hardened for real-world scenarios

The code examples are only meant to help understand concepts and demonstrate possibilities.

By using or referencing this code, you acknowledge that you do so at your own
risk and that the authors assume no liability for any consequences of its use.

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

After this you can access the application at [http://localhost:8500](http://localhost:8500).

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

After this you can access the application at [http://localhost:8500](http://localhost:8500).

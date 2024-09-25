# Local Development with Docker

Preparation:

1. Ensure the git-lfs is installed (GIt Large File System) - see https://git-lfs.github.com
2. Checkout the source code via git



Powermail delivers two options for local development with docker:

* DDEV Support
* Native Docker Support (in2code Standard)



## DDEV

Use DDEV as it is described in the DDEV documentation. Take care, that you use the latest version of DDEV.

1. open the terminal in the root folder of the extension
2. start the project with: `ddev start`
3. import the test data: `ddev initialize` (this step is only needed, if you start the project for the first time)



## Native Docker Support

1. open the terminal in the root folder of the extension
2. Prepare the project with `make install-project`
3. start the http proxy with 
   `docker run -d -v /var/run/docker.sock:/tmp/docker.sock:ro -v ~/.dinghy/certs:/etc/nginx/certs -p 80:80 -p 443:443 -p 19322:19322/udp -e DNS_IP=127.0.0.1 -e CONTAINER_NAME=http-proxy --name http-proxy in2code/http-proxy`
4. Start the project with `make start`
5. If you stop the project use `make stop`

**Tip**: To stop the http-proxy use:
`docker stop $(docker ps -a -q)`
`docker rm http-proxy`

**Note**: You cannot run DDEV Setup next to the "native" docker setup

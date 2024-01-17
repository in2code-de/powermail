# Local Development with Docker

Preparation:

1. Ensure the git-lfs is installed (GIt Large File System) - see https://git-lfs.github.com
2. Checkout the source code via git

Powermail delivers two options for local development with docker:

* DDEV Support

## DDEV

Use DDEV as it is described in the DDEV documentation. Take care, that you use the latest version of DDEV.

1. open the terminal in the root folder of the extension
2. start the project with: `ddev start`
3. import the test data: `ddev initialize` (this step is only needed, if you start the project for the first time)

# Content Field examples

This repository contains a series of small usage examples of the the Content 
Field plugin for Craft CMS.


## Prerequisites

In order to run the examples, you'll need a web server and an empty database that meets the 
[requirements of Craft CMS](https://docs.craftcms.com/v3/requirements.html).


## Installation

This demo includes a `project.yaml` file and a setup script that will execute once
you install the CMS. After cloning this repository, you can follow the official 
[installation instructions](https://docs.craftcms.com/v3/installation.html#step-2-set-the-file-permissions) 
starting at step 2.


### Command line setup

1. Clone this repository.

   ```console
   $ git clone git@github.com:sebastian-lenz/craft-contentfield-examples.git
   ```

2. Run the setup script, the script will collect all required information and
   setup the database.

   ```console
   $ cd craft-contentfield-examples
   $ ./craft setup
   ```
   
3. Setup a web server and point the document root to `craft-contentfield-examples/web`.


### Manual setup

1. Download a copy of this repository and unzip it.  
   https://github.com/sebastian-lenz/craft-contentfield-examples/archive/master.zip

2. Within the unzipped folder, copy the file `.env.example` to `.env`.

3. Open the newly created file `.env` with a text editor and find the line that contains 
   the security key. Set the key to a random password.
   
   ```
   # The secure key Craft will use for hashing and encrypting data
   SECURITY_KEY=""
   ```
   
   If you wish to, you can also fill in the database configuration. Save the file afterwards.
   
4. Setup a web server and point the document root to the folder `web` inside the unzipped 
   folder.

5. Open a web browser and visit `http://<Hostname>/index.php?p=admin/install` (replace
   `<Hostname>` with your web server’s host name) and follow the instructions.


## Questions and feedback

You can find the primary repository of the Content Field plugin here:

https://github.com/sebastian-lenz/craft-contentfield

Please post any questions or issues there.

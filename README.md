# Elevnn


## Installing
### Git
In order to contribute to the repository, make sure you have git installed.
https://chocolatey.org/packages/git.install

### Docker
Local development is done by opening the site in a docker container. You can get docker with the following link:
https://docs.docker.com/docker-for-windows/

Note that you must have Hyper-V enabled (Windows 10 pro+) to install docker.


## Starting the project
Once docker is installed, open a terminal in the root directory of the folder. (I recommend vs-code)
Then, run `docker-compose up` to start the server, and install any needed images. Docker will create a virtual mysql instance 
for you as well.
https://docs.docker.com/compose/

`ctrl+c` will stop the server, or if you run in the backround `docker-compose down`. If you know what you are doing, you can also
manually create and kill containers or volumes, or reinstall images.


## Contributing
### Branch flow for tickets
When you want to test out a new feature, plugin, or theme update, make a git branch `git checkout -b features/<name>` for the feature.
Then, do your changes. When you feel you want to make a checkpoint, commit `git add .; git commit -m "message"`.

### Merging a feature into master
Master is the main development branch everyone should be working off of. Code is never checked directly into master, it is instead
done via a pull request. Please go to [github pull requests](https://github.com/mrthetford/Elevnn/pulls) and create a pull request there
from you feature into master. Then, code review should be done.

When the feature is ready to be merged into master, complete the pull request as a **squash merge**, following the [commit convention](https://www.conventionalcommits.org/en/v1.0.0-beta.2/) (this will be used to create changelogs and manage versions). After the branch is merged, feel free to delete (`git branch -D <name>`) your local branch. 

Don't forget to branch early and often, and `git fetch; git merge origin/master` into your feature branches every day!


## A Guide on Docker
The website is run inside of a virtual machine, with its own copy of files and its own install of wordpress.
We are simply mounting our wp-content folder inside of the virtual machine's `/var/www/html/wp-content` folder.
The Wordpress `wp-content` folder contains all wordpress themes, and plugins, so your environments will be mostly the same
between branches.

The database *(mysql)* is a [`docker volume`](https://docs.docker.com/storage/volumes/) that lives inside of the virtual machine. 
It will persist between sessions because of this, but the database is not easily externally accessible. This means content created
in different environments will not automatically be synchronized, which is actually preferrable -- you don't want developer data
inside of production.

In order to copy production database to development, try generating a [sql dump](https://docs.docker.com/samples/library/mysql/#creating-database-dumps) and restoring it wherever you need.
Please note that you will need to update the database to match your local values. You could also use WP-Staging or WP-Migrate-DB to do this
for you.
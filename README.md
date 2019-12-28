# What this is
This app retrieves (scrapes) all the movies which are included in the German Amazon Prime membership.

After that, it uses a matching algorithm to get their IMDB rating.

# Architecture
## Frontend
Angular2 with capabilities of a progressive web app
Content compression and caching is used for performance optimization.

## Backend
PHP hosted in a free heroku container.

## Database
Free MongoDB (managed) comes to use as storage.
https://mlab.com is a __Database-as-a-Service__ for MongoDB

# Development
## Heroku CLI
First of all, the Heroku CLI needs to be setup as this is the way that I have chosen for deployments. 

Ressource: https://devcenter.heroku.com/articles/heroku-cli

## Getting the health of the application
todo (logs from CLI, heroku-folder-structure)

## How-to maintain the Frontend
todo

## How-to maintain the Backend
### Debugging:
1. introduce the MongoDB Connection String locally
* there are two different approaches:
    1. in MongoDBService.php (survives restarts of your IDE, but there is no risk of commiting this version accidentally)
    2. from the IDE's Run Configuration
    3. setting an own local variable (needs to be set every time the IDE / computer is restarted!)
        * note: this method only works when script is executed from terminal manually  

* in MongoDBService.php:
* comment out the line where MONGODB_URI is populated from an environmental variable
//define('MONGODB_URI', getenv('MONGODB_URI'));
* set the MONGODB_URI yourself by adding following line
```
define('MONGODB_URI', 'mongodb://user:password@ds139964.mlab.com:39964/heroku_n3dfqzx7');
```
* you can find the connection string by using heroku CLI:
````
heroku config
````

* Adding the configuration to the IDE's Run Configuration
    1. Goto Run > Edit Configurations...
    2. In that window: Goto PHP Script > execute.php > Command Line > Environment Variables
    3. Set up a new Environment Variable:
        * __Name:__ MONGODB_URI
        * __Value:__ mongodb://user:password@ds139964.mlab.com:39964/heroku_n3dfqzx7
        * (note that there are not quotes for the value!)

* setting an own local variable (instead of changing the MongoDBService.php)
    * note: this method only works when script is executed from terminal manually
    * note: __not__ this has to be done in the same terminal window where the script is started!
```
$ export MONGODB_URI="mongodb://user:password@ds139964.mlab.com:39964/heroku_n3dfqzx7"
$ env | grep MONGO --color
MONGODB_URI=mongodb://...
```

1. Using the Mlab UI to reset / change / inspect DB tables and fields
* note: it seems like login with heroku user & password stopped working.
* The reason might be that they migrated to Atlas
* you could still try logging in by using the following URL:
```
https://www.mlab.com/databases/heroku_n3dfqzx7
```
* what definitely works is using the SSO-Login from heroku by logging it to the heroku Dashboard and selecting you app, e.g.
```
https://dashboard.heroku.com/apps/imdbprime
```
* And opening the MLab MongoDB URL from the Installed add-ons panel

1. The Execution Decision
* get the current value from DB
    * $howToExecute = DataOperations::evaluateExecution();
* if ($howToExecute === ReturnValues::$shouldStart) {
    * drops current movies collection
    * starts new Amazon Prime Query
* } else if ($howToExecute == ReturnValues::$shouldContinue) {
    * continues execution of Amazon Prime Query where it has stopped / has been aborted
* 
### Scraping Amazon
todo
### Scraping IMDB
todo
### Mongo DB
todo
### The cron job
todo
### The restarting capabilities
todo

-----

1. List example

```
git push
```

2. Second List entry
```
sudo openssl dhparam -out /etc/nginx/dhparam.pem 2048
```
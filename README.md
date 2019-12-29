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

## PHP Composer
To make changes to the packages, the PHP Composer needs to be installed.

* Detailed instructions can be found here: https://getcomposer.org/download/
* choose installation folder (I recommend it is a path in your PATH variable)
```
$ env | grep PATH
PATH=/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin
```
* I decide to install Composer to /usr/local/bin
* Follow the steps: 
```
# Switch to installation directory
cd /usr/local/bin
# Download the installer to the current directory
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
# Verify the installer SHA-384, which you can also cross-check here
php -r "if (hash_file('sha384', 'composer-setup.php') === 'baf1608c33254d00611ac1705c1d9958c817a1a33bce370c0595974b342601bd80b92a3f46067da89e3b06bff421f182') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
# Run the installer
php composer-setup.php
# Remove the installer
php -r "unlink('composer-setup.php');"
```

* after changes to the dependencies, i.e. to the composer.json, you should always update composer.lock file as well
    * this is done by running in the project directory:
```
php composer.phar update
```

* if composer.phar cannot be found, try running
```
php /usr/local/bin/composer.phar update --verbose
```

* For a deployment: The lock file is required in order to guarantee reliable and reproducible installation of dependencies across systems and deploys. It must always be kept in sync with 'composer.json'. Whenever you change 'composer.json', ensure that you perform the following steps locally on your computer:
    1. run 'composer update'
    2. add all changes using 'git add composer.json composer.lock'
    3. commit using 'git commit'.

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
* the search URL: https://www.amazon.de/s?i=prime-instant-video&bbn=3279204031&rh=n%3A3279204031%2Cn%3A3010076031%2Cn%3A3015915031%2Cp_n_ways_to_watch%3A7448695031%2Cp_72%3A3289799031%2Cp_n_entity_type%3A9739119031&lo=list&dc&fst=as%3Aoff&qid=1564341535&rnid=9739118031&ref=sr_pg_4
* the startQuery() function loops through all the Amazon-pages until the end is reached
* for each page, getMoviesFromUrl() is called to scrape movie details (title, directors, actors, year)
* scraping is based on xpath, example:
```
xpath to movie title1:   '//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]  /div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span'
```
* every time Amazon changes how their page is structured, there is a risk that the xpath composition breaks
#### Debugging xpath locations: selecting the element based on xpath-location
* open the above mentioned search URL in chrome
* open Chrome Developer Tools
* go to the console tab
* get the full xpath from the code, for that:
    * concat the strings, e.g. for $movieTitleElem it is $baseQuery + $titleQuerySuffix
    * example: ```//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]/div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span```
    * in chrome, you can select that element by running in console: ```$x('//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]/div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span')[0].innerText```
        * note that you NEED to use single quotes in $x('') because of the double quotes from the @id="search", otherwise it will NOT work!
        * note that you need to add ```[0].innerText``` to the query. The [0] selects the first element (Xpath-selection may return multiple elements) and because we want the caption only, instead of the whole DOM-Object.  
* running the code in Debug mode
    * in primemovies.php, set a breakpoint at the line with the while-loop "while (!$lastMovieOnPage) {"
    * go to execute.php and start running the code in Debug-Mode
    * in case you need to restart frequently, consider adjusting the execute.php-execute-decision by replacing the line:
    ```if ($howToExecute === ReturnValues::$AMAZON_QUERY_SHOULD_START) { ```
    * by this line:
    ```if (true) {``` 

#### Debugging xpath locations: retrieving correct/new/changed xpath-location for an element
* Start Chrome
* Right click the element of interest, for example the title of the movie
* Click 'inspect'
* The Chrome Developer Tools are opened and the element's code-snippet is pre-selected
* Right click the code-snippet > Copy > Copy XPath
* verify by selecting the element in the console
    * example: if the Xpath to the first title element was ```//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[1]/div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span```
    * select it programmatically by doing ```$x('//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[1]/div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span')[0].innerText```

#### Correcting the Xpaths:
* for that, you need to compare the current Xpaths with the new/changed and adjust accordingly
* example:
    * old: ```$x('//*[@id="search"]/div[1]/div[2]/div/span[3]/div[1]/div[1]/div/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span')[0].innerText```
    * new: ```$x('//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[1]/div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span')[0].innerText```
* do it for a couple of elements to find a pattern
* example:
    * movie1 title:     ```//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[1]    /div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span```
    * movie2 title:     ```//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[2]    /div/span/div/div/div[2]/div[2]/div/div[1]/div/div/div[1]/h2/a/span```
    * movie8 director1: ```//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[8]    /div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a[1]```
    * movie8 director2: ```//*[@id="search"]/div[1]/div[2]/div/span[4]/div[1]/div[8]    /div/span/div/div/div[2]/div[2]/div/div[2]/div[2]/div/div/ul/li[2]/span/a[2]```
### Scraping IMDB
todo
### Mongo DB
todo
### The cron job
* The Heroku Scheduler (free) is provisioned for imdbprime
    * accessible from Heroku Dashboard https://dashboard.heroku.com/apps/imdbprime > Installed add-ons
* The Scheduler runs at a Frequency of "Every 10 minutes"
* The Scheduler executes the following command:
```
$ php -f php/execute.php
```

### The restarting capabilities
todo
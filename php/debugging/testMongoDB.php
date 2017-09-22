<?php
require_once ('../commons.php');
require_once ('../DataService/MongoDBService.php');
require_once ('../DataService/DataOperations.php');

require '../../vendor/autoload.php'; // include Composer's autoloader
// Create seed data
$seedData = array(
    array(
        'decade' => '1970s',
        'artist' => 'Debby Boone',
        'song' => 'You Light Up My Life',
        'weeksAtOne' => 10
    ),
    array(
        'decade' => '1980s',
        'artist' => 'Olivia Newton-John',
        'song' => 'Physical',
        'weeksAtOne' => 10
    ),
    array(
        'decade' => '1990s',
        'artist' => 'Mariah Carey',
        'song' => 'One Sweet Day',
        'weeksAtOne' => 16
    ),
);

/*
 * php -r 'echo getenv('MONGODB_URI');'
mongodb://heroku_n3dfqzx7:mypwd@ds139964.mlab.com:39964/heroku_n3dfqzx7
 *
 * php -r 'echo var_dump(parse_url(getenv('MONGODB_URI')));'
array(6) {
  ["scheme"]=>
  string(7) "mongodb"
  ["host"]=>
  string(17) "ds139964.mlab.com"
  ["port"]=>
  int(39964)
  ["user"]=>
  string(15) "heroku_n3dfqzx7"
  ["pass"]=>
  string(26) "mypwd"
  ["path"]=>
  string(16) "/heroku_n3dfqzx7" <--this is the DB name
}
 */

/*
 * Standard single-node URI format:
 * mongodb://[username:password@]host:port/[database]
 */

//DataOperations::markExecutionAs("STARTED");
//echo DataOperations::didRunPrimeMoviesToday();
//DataOperations::dropPrimeMoviesCollection();
//echo DataOperations::whereToContinueAmazonQuery();
//var_dump(DataOperations::getNextMovieAndRemoveItFromDB());
//echo DataOperations::storeMatchedMovie(['a'=>'b']);
//echo DataOperations::replaceOldImdbQueryResults();
//echo MongoDBService::renameCollection(MongoDBCollections::$moviesWithRating, MongoDBCollections::$moviesWithRating);
return;

define('MONGODB_URI', getenv('MONGODB_URI'));
if (MONGODB_URI) {
    $options = array("connectTimeoutMS" => 30000);
    $client = new MongoDB\Client(MONGODB_URI, $options);

    /*
     * First we'll add a few songs. Nothing is required to create the songs
     * collection; it is created automatically when we insert.
     */
    $songs = $client->heroku_n3dfqzx7->songs;
    // To insert a dict, use the insert method.
    $songs->insertMany($seedData);

    /*
     * Then we need to give Boyz II Men credit for their contribution to
     * the hit "One Sweet Day".
    */
    $songs->updateOne(
        array('artist' => 'Mariah Carey'),
        array('$set' => array('artist' => 'Mariah Carey ft. Boyz II Men'))
    );
    /*
     * Finally we run a query which returns all the hits that spent 10
     * or more weeks at number 1.
    */
    $query = array('weeksAtOne' => array('$gte' => 10));
    $options = array(
        "sort" => array('decade' => 1),
    );
    $cursor = $songs->find($query,$options);
    foreach($cursor as $doc) {
        echo 'In the ' .$doc['decade'];
        echo ', ' .$doc['song'];
        echo ' by ' .$doc['artist'];
        echo ' topped the charts for ' .$doc['weeksAtOne'];
        echo ' straight weeks.', "\n";
    }

    /*
     * Deletes complete collection
     */
    //$songs->drop();
}

echo "Finished testMongoDb.php ".nowAsString();
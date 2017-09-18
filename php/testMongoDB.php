<?php
require_once ('commons.php');

require '../vendor/autoload.php'; // include Composer's autoloader
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
mongodb://heroku_n3dfqzx7:4tpqido9fp8p6agaecdqht4il1@ds139964.mlab.com:39964/heroku_n3dfqzx7
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
  string(26) "4tpqido9fp8p6agaecdqht4il1"
  ["path"]=>
  string(16) "/heroku_n3dfqzx7"
}
 */

/*
 * Standard single-node URI format:
 * mongodb://[username:password@]host:port/[database]
 */

if (!getenv('MONGODB_URI')) {
    define('MONGODB_URI', 'mongodb://heroku_n3dfqzx7:4tpqido9fp8p6agaecdqht4il1@ds139964.mlab.com:39964/heroku_n3dfqzx7');
} else {
    define('MONGODB_URI', getenv('MONGODB_URI'));
}
if (MONGODB_URI) {
    $client = new MongoDB\Client(MONGODB_URI);
    var_dump($client->getManager()->getServers());
    echo $client->listDatabases();

    /*
     * First we'll add a few songs. Nothing is required to create the songs
     * collection; it is created automatically when we insert.
     */
    $songs = $client->db->songs;
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
    // Since this is an example, we'll clean up after ourselves.
    $songs->drop();
}

echo "Finished testMongoDb.php ".nowAsString();
<?php
require_once ('commons.php');
require '../vendor/autoload.php'; // include Composer's autoloader


define('MONGODB_URI', getenv('MONGODB_URI'));
define('MY_DB_NAME', "heroku_n3dfqzx7");

class MongoDBService {
    public static $collectionName1 = 'name1';
    public static $collectionNameAmazonPrime = 'foundOnAmazonPrime';

    public static function doSomething($collectionName) {
        if (MONGODB_URI) {
            $options = array("connectTimeoutMS" => 30000);
            $client = new MongoDB\Client(MONGODB_URI, $options);
            $db = $client->selectDatabase(MY_DB_NAME);

            //inserting creates a selected collection, if it doesn't exist
            $collection = $db->selectCollection($collectionName);

            //check, if dataset is already there
            /*$query = array('weeksAtOne' => array('$gte' => 10));
            $options = array(
                "sort" => array('decade' => 1),
            );*/
            $query = array('artist' => array('$eq' => 'Debby Boone2'));
            $cursor = $collection->find($query);
            if (!$cursor->toArray()) {
                $collection->insertOne(array(
                    'decade' => '1970s',
                    'artist' => 'Debby Boone2',
                    'song' => 'You Light Up My Life',
                    'weeksAtOne' => 10
                ));
            }
        } else {
            echo "MONGODB_URI undefined. Cannot connect to Database.";
        }
    }

    private static function insertOne($colName, $doc) {
        $client = self::client();
        if ($client) {
            $db = $client->selectDatabase(MY_DB_NAME);

            //inserting creates a selected collection, if it doesn't exist
            $collection = $db->selectCollection($colName);

            //check, if dataset is already there
            /*$query = array('weeksAtOne' => array('$gte' => 10));
            $options = array(
                "sort" => array('decade' => 1),
            );*/

            //TODO: nach mehreren Eigenschaften suchen?
            $query = array('movie' => array('$eq' => $doc['movie']));
            $cursor = $collection->find($query);
            if (!$cursor->toArray()) {
                $collection->insertOne($doc);
            }
        }
    }

    private static function client() {
        //TODO: how to reuse an existing connection?
        if (MONGODB_URI) {
            $options = array("connectTimeoutMS" => 30000);
            $client = new MongoDB\Client(MONGODB_URI, $options);
            return $client;
        } else {
            echo "MONGODB_URI undefined. Cannot connect to Database.";
            return null;
        }
    }

    public static function storeMovies($movie) {
        // add movie/displayedMovies as last array entry/entries
        if (isset($movie['movie'])) {
            self::insertOne(self::$collectionNameAmazonPrime, $movie);
        } else {
            foreach ($movie as $movieEntry) {
                self::insertOne(self::$collectionNameAmazonPrime, $movieEntry);
            }
        }
    }
}

echo "Finished testMongoDb.php ".nowAsString();
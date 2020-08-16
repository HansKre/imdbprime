<?php
require_once (realpath(dirname(__FILE__)).'/../commons.php');
require_once (realpath(dirname(__FILE__)).'/../../vendor/autoload.php'); // include Composer's autoloader


define('MONGODB_URI', getenv('MONGODB_URI'));
define('MY_DB_NAME', "imdbprime");

class MongoDBService {
    private static function db() {
        if (MONGODB_URI) {
            $uriOptions = array(
                "connectTimeoutMS" => 30000,
                'retryWrites' => false
            );
            // returned results shall be arrays always. Don't return BSONDocuments
            $driverOptions= [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array',
                    'array' => 'array'
                ]
            ];
            $client = new MongoDB\Client(MONGODB_URI, $uriOptions, $driverOptions);
            $db = $client->selectDatabase(MY_DB_NAME);
            return $db;
        } else {
            echo "MONGODB_URI undefined. Cannot connect to Database.";
            return null;
        }
    }

    public static function insertOneUnique($colName, $doc) {
        $db = self::db();
        if ($db) {
            //inserting creates and selects collection, if it doesn't exist already
            $collection = $db->selectCollection($colName);

            try {
                $cursor = $collection->find($doc);

                //insert only, if it does not exist yet
                $records = iterator_to_array($cursor);
                if (count($records) == 0) {
                    return $collection->insertOne($doc)->isAcknowledged();
                } else {
                    return false;
                }
            } catch (Exception $e) {
                myLog('Exception while trying to search & insert into the DB in MongoDBService::insertOneUnique. Exception:');
                myLog($e->getMessage());
            }
        }
        return false;
    }

    /**
     * Tries to find a document which matches the filter. The fields of this document are then updated.
     * If no matching document is found, a new one is inserted.
     *
     * Note: updateOne() is not used here since it is hard to tell which one will be updated (probably the one
     * with the lowes _id).
     *
     * @param string $colName
     * @param array $filter This is the filter which is used to find the document which needs to be updated.
     * @param array $content These are the fields which need to be updated.
     * @return bool returns true if update was successful.
     */
    public static function updateMany(string $colName, array $filter, array $content) {
        //http://php.net/manual/de/mongocollection.update.php
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);

            $update = [
                '$set' => $content
            ];

            $options = [
                /*If no document matches $filter, a new document will be inserted.*/
                'upsert' => true,
            ];

            try {
                $collection->updateMany($filter, $update, $options);
            } catch (Exception $e) {
                echo $e;
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public static function find(string $colName, array $filter) : array {
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);
            $options = [
                /* prevent _id from being part of the results */
                'projection' => [
                    '_id' => 0,
                ],
            ];
            try {
                return $collection->find($filter, $options)->toArray();
            } catch (Exception $e) {
                echo $e;
                return null;
            }
        } else {
            return null;
        }
    }

    public static function findAll(string $colName) : array {
        $filter = [];
        return self::find($colName, $filter);
    }

    public static function findOne(string $colName, array $filter) {
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);
            try {
                return $collection->findOne($filter);
            } catch (Exception $e) {
                echo $e;
                return null;
            }
        } else {
            return null;
        }
    }

    public static function findOneMax(string $colName, string $sortKey) {
        $db = self::db();
        if ($db) {
            //inserting creates a selected collection, if it doesn't exist
            $collection = $db->selectCollection($colName);

            $filter=[];
            $options = [
                /* -1 is for DESC */
                'sort' => [$sortKey => -1]
            ];

            $result = $collection->findOne($filter, $options);

            $max = $result[$sortKey];
            return $max;
        } else {
            echo "MONGODB_URI undefined. Cannot connect to Database.";
        }
        return null;
    }

    public static function findManyMax(string $colName, string $sortKey, int $n) {
        $db = self::db();
        if ($db) {
            //inserting creates a selected collection, if it doesn't exist
            $collection = $db->selectCollection($colName);

            /* Select only documents authord by "bjori" with at least 100 views */
            $filter = [];
            $options = [
                /* Return the documents in descending order of searchPage */
                'sort' => [$sortKey => -1],
                /* Limit to results to  ... */
                'limit' => $n,
            ];

            $cursor = $collection->find($filter, $options);
            $results = [];
            foreach ($cursor as $result) {
                $results[] = $result[$sortKey];
            }
            return $results;

        } else {
            echo "MONGODB_URI undefined. Cannot connect to Database.";
        }
        return null;
    }

    public static function drop(string $colName) {
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);
            try {
                $collection->drop();
            } catch (Exception $e) {
                echo $e;
            }
        }
    }

    public static function findOneAndDelete(string $colName, $filter) {
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);
            $options = [
                /* prevent _id from being part of the results */
                'projection'=>[
                    '_id'=>0,
                ],
            ];
            try {
                /* Finds a single document and deletes it, returning the original.
                The document to return may be null if no document matched the filter.*/
                return $collection->findOneAndDelete($filter, $options);
            } catch (Exception $e) {
                echo $e;
                return null;
            }
        }
    }

    public static function hasCollection(string $colName) {
        $db = self::db();
        if ($db) {
            foreach ($db->listCollections() as $collection) {
                if ($collection->getName() == $colName) {
                    return true;
                }
            }
            return false;
        }
    }

    public static function renameCollection(string $from, string $to) {
        // to use the renameCollection, we need to login to the 'admin' database
        $options = array("connectTimeoutMS" => 30000);
        $client = new MongoDB\Client(MONGODB_URI, $options);
        $db = $client->selectDatabase('admin');
        if ($db) {
            try {
                $db->command(array('renameCollection'=> MY_DB_NAME.".".$from,'to'=>MY_DB_NAME.".".$to));
                return true;
            } catch (Exception $e) {
                echo $e;
                return false;
            }
        } else {
            echo "Could not connect to the admin DB. Rename Collection failed";
            return false;
        }
    }

    public static function count(string $colName, $filter) {
        $db = self::db();
        if ($db) {
            $collection = $db->selectCollection($colName);
            try {
                return $collection->count($filter);
            } catch (Exception $e) {
                echo $e;
                return null;
            }
        }
    }
}

/* QUERY TYPES
$result = $collection->find( [ 'name' => 'Hinterland', 'brewery' => 'BrewDog' ] );
*/

//check, if dataset is already there
/*$filter = array('weeksAtOne' => array('$gte' => 10));
$options = array(
    "sort" => array('decade' => 1),
);*/

/*
 * You can use the $type operator with $not in your query to exclude docs where age is a string.
 * $cursor = $collection->find(array('age' => array('$not' => array('$type' => 2))), array('age' => 1));
 */

/*
 * $filter = [
                    'author' => 'bjori',
                    'views' => [
                        '$gte' => 100,
                    ],
];
 */

/* Search for a range: http://php.net/manual/de/mongocollection.find.php
https://www.techcoil.com/blog/getting-documents-from-mongodb-collections-with-php/
// search for documents where 5 < x < 20
$rangeQuery = array('x' => array( '$gt' => 5, '$lt' => 20 ));
*/
<?php
ini_set('max_execution_time', 3600);
require_once ('commons.php');


/*
 todo: primemovies "schöner"
todo: execute restartfähig
todo: videos nach jeder page schreiben
todo: primefaces: bei zuletzt aufgehörter page wiedereinsetzen
todo: director & actor crawlen
*/
class PrimeMovies {
    private $i;
    private $executionId;
    private $startTime;
    private $moviesFound;

    public const PRIME_OUTPUT_MOVIES_TXT = './output/movies.txt';

    /*
     * On Openshift, scripts get aborted after 20mins, thus to avoid data loss,
     * no further calculation happens after 19 minutes.
     */
    private function hasTime() {
        $now = time();
        $elapsedMinutes = ($now - $this->startTime) / 60;
        if ($elapsedMinutes > 18) {
            return false;
        }
        return true;
    }

    private function log($message) {
        myLog($this->executionId . " " . $message);
    }

    private function extractYear ($resultLi, $movieTitle) {
        //OV]2017EUR
        $stringValue = $resultLi->nodeValue;
        if ($stringValue) {
            $position = strpos($stringValue, $movieTitle);
            if ($position !== false) {
                $year = substr($stringValue,$position + strlen($movieTitle),4);
                if ($year) {
                    if (!contains($year,"EUR")) {
                        return $year;
                    }
                }
            }
        }
        return "0";
    }

    private function getMoviesFromUrl($url, &$reachedEnd) {
        $movies = array();

        //Load the HTML page
        $html = file_get_contents($url);

        //Create a new DOM document
        $dom = new DOMDocument;

        //Parse the HTML. The @ is used to suppress any parsing errors
        //that will be thrown if the $html string isn't valid XHTML.
        if (@$dom->loadHTML($html)) {
            /*<div id="atfResults" class="a-row s-result-list-parent-container"><ul id="s-results-list-atf" class="s-result-list s-col-1 s-col-ws-1 s-result-list-hgrid s-height-equalized s-list-view s-text-condensed"><li id="result_6368" data-asin="B06XRLC6N7" class="s-result-item celwidget "><div class="s-item-container"><div class="a-fixed-left-grid"><div class="a-fixed-left-grid-inner" style="padding-left:218px"><div class="a-fixed-left-grid-col a-col-left" style="width:218px;margin-left:-218px;_margin-left:-109px;float:left;"><div class="a-row"><div aria-hidden="true" class="a-column a-span12 a-text-center"><a class="a-link-normal a-text-normal" href="https://www.amazon.de/Blueberry-Hunt-OV-Naseeruddin-Shah/dp/B06XRLC6N7/ref=sr_1_6369?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6369"><img alt="The Blueberry Hunt [OV]" src="https://images-eu.ssl-images-amazon.com/images/I/61O0uNFt5pL._PI_PJPrime-Sash-Extra-Large-2017,TopLeft,0,0_AC_US218_.jpg" class="s-access-image cfMarker" height="218" width="218"></a><div class="a-section a-spacing-none a-text-center"></div></div></div></div><div class="a-fixed-left-grid-col a-col-right" style="padding-left:2%;*width:97.6%;float:left;"><div class="a-row a-spacing-small"><div class="a-row a-spacing-none"><a class="a-link-normal s-access-detail-page  s-color-twister-title-link a-text-normal" title="The Blueberry Hunt [OV]" href="https://www.amazon.de/Blueberry-Hunt-OV-Naseeruddin-Shah/dp/B06XRLC6N7/ref=sr_1_6369?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6369"><h2 data-attribute="The Blueberry Hunt [OV]" data-max-rows="0" class="a-size-medium s-inline  s-access-title  a-text-normal">The Blueberry Hunt [OV]</h2></a><span class="a-letter-space"></span><span class="a-letter-space"></span><span class="a-size-small a-color-secondary">2016</span></div></div><div class="a-row"><div class="a-column a-span7"><div class="a-row a-spacing-none"><span class="a-size-small a-color-tertiary">In Ihrer Prime-Mitgliedschaft enthalten.</span></div><div class="a-row a-spacing-mini"><a class="a-link-normal a-text-normal" href="https://www.amazon.de/Blueberry-Hunt-OV-Naseeruddin-Shah/dp/B06XRLC6N7/ref=sr_1_6369_dvt_1_wnzw?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6369"><span class="a-size-base">Jetzt ansehen</span></a></div><div class="a-row a-spacing-top-mini a-spacing-mini"><span class="a-declarative" data-action="s-watchlist-add" data-s-watchlist-add="{&quot;asin&quot;:&quot;B06XRLC6N7&quot;,&quot;prodType&quot;:&quot;movie&quot;,&quot;n&quot;:&quot;6369&quot;}"><span class="a-button a-button-small"><span class="a-button-inner"><input class="a-button-input" type="submit"><span class="a-button-text" aria-hidden="true"><span class="s-padding-left-large s-padding-right-large">
                    Zur Watchlist hinzufügen</span>
            </span></span></span></span><span class="a-declarative" data-action="s-watchlist-remove" data-s-watchlist-remove="{&quot;asin&quot;:&quot;B06XRLC6N7&quot;,&quot;prodType&quot;:&quot;movie&quot;,&quot;n&quot;:&quot;6369&quot;}"><span class="a-button a-button-small s-hidden"><span class="a-button-inner"><input class="a-button-input" type="submit"><span class="a-button-text" aria-hidden="true"><span class="s-padding-left-large s-padding-right-large">
                    Aus Watchlist entfernen</span>
            </span></span></span></span></div></div><div class="a-column a-span5 a-span-last"><div class="a-fixed-left-grid"><div class="a-fixed-left-grid-inner" style="padding-left:75px"><div class="a-text-left a-fixed-left-grid-col a-col-left" style="width:75px;margin-left:-75px;_margin-left:-37.5px;float:left;"><span class="a-size-small a-color-secondary">In der Hauptrolle:</span></div><div class="a-text-left a-fixed-left-grid-col a-col-right" style="padding-left:0%;*width:99.6%;float:left;"><span class="a-size-small a-color-secondary"><a class="a-size-small a-link-normal a-text-normal" href="/s/ref=sr_ctrb_default_srch_lnk_1_?rh=i%3Aprime-instant-video%2Cn%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031%2Ck%3AVipin+Sharma&amp;bbn=3279204031&amp;keywords=Vipin+Sharma&amp;ie=UTF8&amp;qid=1500111604">Vipin&nbsp;Sharma</a>, <a class="a-size-small a-link-normal a-text-normal" href="/s/ref=sr_ctrb_default_srch_lnk_1_?rh=i%3Aprime-instant-video%2Cn%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031%2Ck%3AKarthik+Elangovan&amp;bbn=3279204031&amp;keywords=Karthik+Elangovan&amp;ie=UTF8&amp;qid=1500111604">Karthik&nbsp;Elangovan</a>, et al.</span></div></div></div><div class="a-fixed-left-grid"><div class="a-fixed-left-grid-inner" style="padding-left:75px"><div class="a-text-left a-fixed-left-grid-col a-col-left" style="width:75px;margin-left:-75px;_margin-left:-37.5px;float:left;"><span class="a-size-small a-color-secondary">Regie:</span></div><div class="a-text-left a-fixed-left-grid-col a-col-right" style="padding-left:0%;*width:99.6%;float:left;"><span class="a-size-small a-color-secondary"><a class="a-size-small a-link-normal a-text-normal" href="/s/ref=sr_ctrb_default_srch_lnk_1_?rh=i%3Aprime-instant-video%2Cn%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031%2Ck%3AAnup+Kurian&amp;bbn=3279204031&amp;keywords=Anup+Kurian&amp;ie=UTF8&amp;qid=1500111604">Anup&nbsp;Kurian</a></span></div></div></div><div class="a-fixed-left-grid"><div class="a-fixed-left-grid-inner" style="padding-left:75px"><div class="a-text-left a-fixed-left-grid-col a-col-left" style="width:75px;margin-left:-75px;_margin-left:-37.5px;float:left;"><span class="a-size-small a-color-secondary">Laufzeit:</span></div><div class="a-text-left a-fixed-left-grid-col a-col-right" style="padding-left:0%;*width:99.6%;float:left;"><span class="a-size-small a-color-secondary">1 Std. 54 Min.</span></div></div></div></div></div></div></div></div></div></li>


            <li id="result_6369" data-asin="B01G2HCOBW" class="s-result-item celwidget "><div class="s-item-container"><div class="a-fixed-left-grid"><div class="a-fixed-left-grid-inner" style="padding-left:218px"><div class="a-fixed-left-grid-col a-col-left" style="width:218px;margin-left:-218px;_margin-left:-109px;float:left;"><div class="a-row"><div aria-hidden="true" class="a-column a-span12 a-text-center"><a class="a-link-normal a-text-normal" href="https://www.amazon.de/Clash-Clans-Healer-Tolerable-Entertainment/dp/B01G2HCOBW/ref=sr_1_6370?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6370"><img alt="Clash of Clans: The Healer [OV]" src="https://images-eu.ssl-images-amazon.com/images/I/51KymZWHqNL._PI_PJPrime-Sash-Extra-Large-2017,TopLeft,0,0_AC_US218_.jpg" class="s-access-image cfMarker" height="218" width="218"></a><div class="a-section a-spacing-none a-text-center"></div></div></div></div><div class="a-fixed-left-grid-col a-col-right" style="padding-left:2%;*width:97.6%;float:left;"><div class="a-row a-spacing-small"><div class="a-row a-spacing-none"><a class="a-link-normal s-access-detail-page  s-color-twister-title-link a-text-normal" title="Clash of Clans: The Healer [OV]" href="https://www.amazon.de/Clash-Clans-Healer-Tolerable-Entertainment/dp/B01G2HCOBW/ref=sr_1_6370?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6370"><h2 data-attribute="Clash of Clans: The Healer [OV]" data-max-rows="0" class="a-size-medium s-inline  s-access-title  a-text-normal">Clash of Clans: The Healer [OV]</h2></a><span class="a-letter-space"></span><span class="a-letter-space"></span><span class="a-size-small a-color-secondary">2016</span></div></div><div class="a-row"><div class="a-column a-span7"><div class="a-row a-spacing-none"><span class="a-size-small a-color-tertiary">In Ihrer Prime-Mitgliedschaft enthalten.</span></div><div class="a-row a-spacing-mini"><a class="a-link-normal a-text-normal" href="https://www.amazon.de/Clash-Clans-Healer-Tolerable-Entertainment/dp/B01G2HCOBW/ref=sr_1_6370_dvt_1_wnzw?s=instant-video&amp;ie=UTF8&amp;qid=1500111604&amp;sr=1-6370"><span class="a-size-base">Jetzt ansehen</span></a></div><div class="a-row a-spacing-top-mini a-spacing-mini"><span class="a-declarative" data-action="s-watchlist-add" data-s-watchlist-add="{&quot;asin&quot;:&quot;B01G2HCOBW&quot;,&quot;prodType&quot;:&quot;movie&quot;,&quot;n&quot;:&quot;6370&quot;}"><span class="a-button a-button-small"><span class="a-button-inner"><input class="a-button-input" type="submit"><span class="a-button-text" aria-hidden="true"><span class="s-padding-left-large s-padding-right-large">
                    Zur Watchlist hinzufügen</span>*/
            $resultLiArray = getElementsBy($dom, "li","id","result_");

            foreach($resultLiArray as $resultLi) {
                // <h2 data-attribute="The Blueberry Hunt [OV]" data-max-rows="0" class="a-size-medium s-inline  s-access-title  a-text-normal">The Blueberry Hunt [OV]</h2>
                $h2Elems = $resultLi->getElementsByTagName('h2');

                //Iterate over the extracted h2Elems and extract the movie titles
                foreach ($h2Elems as $h2Elem) {
                    //Extract movie title out of the "data-attribute" attribute.
                    $movieTitle = $this->extractMovieTitle($h2Elem);
                    if ($movieTitle) {
                        if ($this->isMovieLongerThanOneHour($resultLi)) {
                            $year = $this->extractYear($resultLi, $movieTitle);
                            if (contains($movieTitle, "Jazz The Glass")) {
                                echo "";
                            }
                            $director = $this->extractDirector($resultLi);
                            $actors = $this->extractActors($resultLi);
                            $movies[] = array('year'=>$year, 'movie'=>$movieTitle, 'director'=>$director, 'actors'=>$actors, 'searchPage'=>$this->i);
                        }
                    }
                }
            }
            $reachedEnd = $this->isLastResultPage($dom);
            return $movies;
        }
        return null;
    }

    private function startTime() {
        $this->startTime = microtime(true);
    }

    private function stopTime() {
        $executionTime = (microtime(true) - $this->startTime) / 60;
        myLog($this->executionId . ' Primemovies.php finished. Total Execution Time: ' . $executionTime . ' Minutes. Movies found: ' . $this->moviesFound);
    }

    private function saveToFile($movies) {
        //delete old file
        unlink(self::PRIME_OUTPUT_MOVIES_TXT);

        //write to text file
        $string_data = serialize($movies);
        file_put_contents(self::PRIME_OUTPUT_MOVIES_TXT, $string_data);
    }

    public function start($startAt) {
        $this->setCounter($startAt);

        $this->log("Starting primemovies.php script with i = " . $this->i);
        $this->startTime();

        $movies = array();
        $reachedEnd = false;

        $sleepTime = ONESECOND;
        while ($reachedEnd == false) {
            myLog($this->executionId . " Parsing page " . $this->i);
            $newMovies = $this -> getMoviesFromUrl($this->getSearchUrl(), $reachedEnd);
            print_r($newMovies);
            if (!empty($newMovies)) {
                $movies = array_merge($movies, $newMovies);
                $this->i++;
                if ($sleepTime > 2 * ONESECOND) {
                    $sleepTime -= 500000;
                }
            } else {
                $sleepTime += 500000;
                myLog($this->executionId . " , " . $sleepTime/1000000);
            }
            usleep($sleepTime);
        }

        $this->moviesFound = count($movies);
        $this->saveToFile($movies);
        $this->stopTime();

        return true;
    }

    public function __construct($executionId) {
        if ($executionId) {
            $this->executionId = $executionId;
        } else {
            $this->executionId = rand();
        }
    }

    private function setCounter($startAt)
    {
        if ($startAt) {
            $this->i = $startAt;
        } else {
            $this->i = 1;
        }
    }

    private function getSearchUrl() {
        // https://www.amazon.de/s/ref=sr_pg_399?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=399&bbn=3279204031&ie=UTF8
        $part1 = 'https://www.amazon.de/s/ref=sr_pg_';
        $part2 = '?fst=as%3Aoff&rh=n%3A3279204031%2Cp_n_ways_to_watch%3A7448695031%2Cn%3A%213010076031%2Cn%3A3015915031&page=';
        $part3 = '&bbn=3279204031&ie=UTF8';

        return $part1 . $this->i . $part2 . $this->i . $part3;
    }

    private function extractMovieTitle($h2Elem) {
        $movieTitle = $h2Elem->getAttribute('data-attribute');
        if (strlen($movieTitle) > 1) {
            return $movieTitle;
        }
        return null;
    }

    private function isMovieLongerThanOneHour($resultLi) {
        //resultLi->nodeValue => Finding Fidel [OV]2016EUR 0,00Mit einem Prime Abo ansehenEUR 4,99 - EUR 11,99Leihen oder  kaufenRegie:Bay WeymanLaufzeit:1 Std. 29 Min.
        $position = strpos($resultLi->nodeValue, "Laufzeit");
        $laufzeit = substr($resultLi->nodeValue, $position + strlen("Laufzeit:"));
        // "1 Std. 55 Min." oder " 52 Min."
        if (strpos($laufzeit, 'Std') !== false) {
            return true;
        }
        return false;
    }

    private function extractDirector($resultLi) {
        //resultLi->nodeValue => Finding Fidel [OV]2016EUR 0,00Mit einem Prime Abo ansehenEUR 4,99 - EUR 11,99Leihen oder  kaufenRegie:Bay WeymanLaufzeit:1 Std. 29 Min.
        $position = strpos($resultLi->nodeValue, "Regie");
        if ($position !== false) {
            $regie = substr($resultLi->nodeValue, $position + strlen("Regie:"));

            $position = strpos($regie, "Laufzeit");
            $regie = substr($regie, 0, $position);

            if (contains($regie, ",")) {
                $directors = explode(",", $regie);
                $returnDirectors = array();
                foreach ($directors as $director) {
                    if (!contains($director, "et al.")) {
                        $returnDirectors[] = trim($director);
                    }
                }
                return $returnDirectors;
            }
            return $regie;
        }
        return null;
    }

    private function extractActors($resultLi) {
        //resultLi->nodeValue => Finding Fidel [OV]2016EUR 0,00Mit einem Prime Abo ansehenEUR 4,99 - EUR 11,99Leihen oder  kaufenRegie:Bay WeymanLaufzeit:1 Std. 29 Min.
        $position = strpos($resultLi->nodeValue, "Hauptrolle");
        if ($position !== false) {
            $actor = substr($resultLi->nodeValue, $position + strlen("Hauptrolle:"));

            $position = strpos($actor, "Regie");
            if ($position !== false) {
                $actor = substr($actor, 0, $position);
            } else {
                $position = strpos($actor, "Laufzeit");
                if ($position !== false) {
                    $actor = substr($actor, 0, $position);
                }
            }

            if (contains($actor, ",")) {
                $actors = explode(",", $actor);
                $returnActors = array();
                foreach ($actors as $actor) {
                    if (!contains($actor, "et al.")) {
                        $returnActors[] = trim($actor);
                    }
                }
                return $returnActors;
            }
            return $actor;
        }
        return null;
    }

    private function isLastResultPage($dom) {
    //div class="proceedWarning"><span>Sie haben das Ende unseres besten Suchergebnis-Sets erreicht.</span></div>
        $warningsDivElems = getElementsByClass($dom, 'div', 'proceedWarning');
        if ($warningsDivElems) {
            foreach ($warningsDivElems as $warningsDivElem) {
                //we are looking for 'Sie haben das Ende unseres besten Suchergebnis-Sets erreicht.'
                if (strpos($warningsDivElem->nodeValue, 'Ende') !== false) {
                    return true;
                }
            }
        }
        return false;
    }
}
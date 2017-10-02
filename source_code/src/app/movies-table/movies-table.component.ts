import { Component, OnInit } from '@angular/core';
import {FADE_IN_OUT_ANIMATION} from "../animations/fade-in-out.animation";
import {Movie} from "../structures/movie";
import {WebService} from "../services/web.service";
import {MdSnackBar, Sort} from "@angular/material";
import {ValuesService} from "../services/values.service";
import {IsOnlineService} from "../services/is-online.service";

@Component({
    selector: 'app-movies-table',
    templateUrl: './movies-table.component.html',
    styleUrls: ['./movies-table.component.css'],
    animations: [
        FADE_IN_OUT_ANIMATION,
    ]
})
export class MoviesTableComponent implements OnInit {

    conditionalAnimation:string = "in";

    constructor(private webService: WebService,
                private valuesService: ValuesService,
                private isOnlineService: IsOnlineService,
                public snackBar: MdSnackBar,) { }

    calcMaxRatingCount() {
        if (this.valuesService.filteredMovies) {
            let max:number = 0;
            this.valuesService.filteredMovies.forEach(function (entry) {
                if (entry.ratingCount > max) {
                    max = entry.ratingCount;
                }
            });
            this.valuesService.maxRatingCount = max;
        }
    }

    calcMaxRatingValue() {
        if (this.valuesService.filteredMovies) {
            let max:number = 0;
            this.valuesService.filteredMovies.forEach(function (entry) {
                let n:number = parseFloat(entry.ratingValue);
                if (n > max) {
                    max = n;
                }
            });
            this.valuesService.maxRatingValue = max;
        }
    }

    calcMaxYear() {
        if (this.valuesService.filteredMovies) {
            let maxY:number = 0;
            this.valuesService.filteredMovies.forEach(function (entry) {
                // MAX
                if (entry.year > maxY) {
                    maxY = entry.year;
                }
            });
            this.valuesService.maxYear = maxY;
        }
    }

    calcMinYear() {
        if (this.valuesService.allMovies) {
            let minY:number = 10000;
            this.valuesService.allMovies.forEach(function (entry) {
                // MIN
                if ((entry.year > 1900) && (entry.year < minY)) {
                    minY = entry.year;
                }
            });
            this.valuesService.minYear = minY;
        }
    }

    calcMinMaxValues() {
        this.calcMaxRatingCount();
        this.calcMaxYear();
        this.calcMaxRatingValue();
    }

    ngOnInit() {
        this.isOnlineService.isOnlineObserveable()
            .subscribe((isOnline:boolean) => this.onlineChanged(isOnline));
        this.preLoadMoviesFromLocalStorage();
        this.registerForWebRequest();

        //subscribe to data changes of valuesService
        this.valuesService.minYearValueFilterObservable
            .subscribe((newValue:number) => this.onYearChanged(newValue));

        this.valuesService.minRatingCountFilterObservable
            .subscribe((newValue:number) => this.onRatingCountChanged(newValue));

        this.valuesService.minRatingValueFilterObservable
            .subscribe((newValue:number) => this.onRatingValueChanged(newValue));
    }

    onlineChanged(isOnline:boolean) {
        if (isOnline && this.valuesService.shouldLoadMoviesFromServer) {
            this.registerForWebRequest();
        }
    }

    preLoadMoviesFromLocalStorage() {
        if (localStorage && localStorage.movies
            && (localStorage.movies != "undefined")) {
            this.valuesService.allMovies = JSON.parse(localStorage.movies);
            if (this.valuesService.allMovies) {
                this.filterAndSetMovies();
                this.calcMinYear();
                this.calcMinMaxValues();
            }
        }
    }

    storeMoviesToLocalStorage() {
        if (localStorage) {
            localStorage.movies = JSON.stringify(this.valuesService.allMovies);
        }
    }

    registerForWebRequest() {
        this.webService.getPromise().then(function (movies) {
            this.resolvePromisedRequest(movies);
        }.bind(this), function (error) {
            console.log("Promise rejected: " + error.toString());
        }.bind(this));
    }

    private resolvePromisedRequest(movies) {
        this.valuesService.allMovies = movies as any as Movie[];
        this.filterAndSetMovies(true);
        this.storeMoviesToLocalStorage();
        this.valuesService.shouldLoadMoviesFromServer = false;
        this.calcMinYear();
        this.calcMinMaxValues();
    }

    public filterAndSetMovies(incrementally?:boolean) {
        let filter:string = this.valuesService.searchString.toUpperCase().trim();
        this.valuesService.filteredMovies =
            this.valuesService.allMovies.filter(
                movie =>
                    (
                        (movie.ratingCount >= this.valuesService.minRatingCountFilter) &&
                        ((parseFloat(movie.ratingValue) * 10) >= (this.valuesService.minRatingValueFilter * 10)) &&
                        (movie.year >= this.valuesService.minYearValueFilter) &&
                        (movie.movie.toUpperCase().indexOf(filter) > -1)
                    )
            );

        // iPhone fix: fixes issues with focused input field while onscreen keyboard is visible
        if (this.valuesService.filteredMovies.length < 60) {
            window.scrollTo(0, 0);
        }

        this.sortData();

        if (incrementally) {
            this.setDisplayedMoviesIncrementally();
        } else {
            this.setDisplayedMovies();
        }
        this.calcMinMaxValues();
    }

    setDisplayedMoviesIncrementally() {
        // since displayedMovies is in a binding-relation to the data-table:
        // if the original displyedMovies array is resetted, the the DOM elements get resetted too
        // this looks like a reload of the whole page
        // if this happens after the request-Promise is returned, the user sees a reload
        // we wan't to avoid this unexpected reload

        let indicesOfFoundDisplayedMovies:number[] = [];

        for (let i = 0; i < this.valuesService.displayedMovies.length; i++) {
            // try to find old elemt-i in new array
            let movie:Movie = this.valuesService.filteredMovies.find(element => element.movie === this.valuesService.displayedMovies[i].movie);
            if (movie) {
                indicesOfFoundDisplayedMovies.push(this.valuesService.filteredMovies.indexOf(movie));
            } else {
                // remove movie
                this.valuesService.displayedMovies.slice(i, 1);
            }
        }
        // add movies to displayedMovies, if they are new in the filtered list
        let min:number = Math.min(100, this.valuesService.filteredMovies.length);
        for (let i = 0; i < min; i++) {
            if (indicesOfFoundDisplayedMovies.indexOf(i) == -1) {
                this.valuesService.displayedMovies.push(this.valuesService.filteredMovies[i]);
            }
        }
    }

    setDisplayedMovies() {
        this.valuesService.displayedMovies = [];

        let min:number = Math.min(100, this.valuesService.filteredMovies.length);

        for (let i = 0; i < min; i++) {
            this.valuesService.displayedMovies.push(this.valuesService.filteredMovies[i]);
        }
    }

    sort: Sort;
    sortData(sort?: Sort) {
        /* if mdSortDisableClear is not used, there is a unsorted state which should be handled here
        if (!sort.active || sort.direction == '') {
            this.sortedData = data;
            return;
        }*/

        let _sort: Sort;
        if (sort) {
            this.sort = sort;
            _sort = sort;
        } else {
            _sort = this.sort;
        }
        console.log("_sort:", _sort, "this.sort: ", this.sort);

        if (_sort) {
            // there is no sort during initial loading
            this.valuesService.filteredMovies = this.valuesService.filteredMovies.sort((a, b) => {
                let isAsc = _sort.direction == 'asc';
                switch (_sort.active) {
                    case 'movie': return compare(a.movie, b.movie, isAsc);
                    case 'year': return compare(+a.year, +b.year, isAsc);
                    case 'rating': return compare(+a.ratingValue, +b.ratingValue, isAsc);
                    case 'count': return compare(+a.ratingCount, +b.ratingCount, isAsc);
                    default: return 0;
                }
            });
        }
    }

    sortDataAndSetMovies(sort: Sort) {
        this.sortData(sort);
        this.setDisplayedMovies();
    }

    onRatingCountChanged(newValue:number) {
        this.showSnackbar("Minimum Rating Count set to:",
            newValue, true);
        this.filterAndSetMovies();
    }

    onRatingValueChanged(newValue:number) {
        this.showSnackbar("Minimum Rating Value set to:",
            newValue, true);
        this.filterAndSetMovies();
    }

    onYearChanged(newValue:number) {
        this.showSnackbar("Minimum Year set to:",
            newValue, false);
        this.filterAndSetMovies();
    }

    private showSnackbar(message:string, sliderValue: number, toLocale:boolean) {
        let valueToShow = (toLocale ? sliderValue.toLocaleString('en') : sliderValue.toString());
        this.snackBar.open(message + " " + valueToShow, '', {
            duration: 1000,
        });
    }
}

function compare(a, b, isAsc) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
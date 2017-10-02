import { Component, Input, OnInit } from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";
import { MdSnackBar, Sort } from "@angular/material";
import { IsOnlineService } from "../services/is-online.service";
import { FADE_IN_OUT_ANIMATION } from "../animations/fade-in-out.animation";

@Component({
    selector: 'app-movies-container',
    templateUrl: './movies.container.component.html',
    styleUrls: ['./movies.container.component.css'],
    animations: [
        FADE_IN_OUT_ANIMATION,
    ]
})

// it is possible to animate the transition between 2 states, just use animate()
// for animating from void, specify the starting state for animation
// it is also possible to specify the end state when animation finishes
// ... after that, animation transitions immediately to the target state?

export class MoviesContainerComponent implements OnInit {
    @Input() isParentLoading:boolean = true;

    displayedMovies: Movie[] = [];
    filteredMovies: Movie[];
    allMovies: Movie[];

    shouldLoadMoviesFromServer:boolean = true;

    maxRatingCount: number;
    maxRatingValue: number;
    maxYear: number;
    minYear: number;

    minRatingCountFilter:number = 10000;
    minRatingValueFilter:number = 6;
    minYearValueFilter:number = 2000;

    searchString:string ="";

    conditionalAnimation:string = "in";

    constructor(private webService: WebService,
                private isOnlineService: IsOnlineService,
                public snackBar: MdSnackBar) {
    }

    calcMaxRatingCount() {
        if (this.filteredMovies) {
            let max:number = 0;
            this.filteredMovies.forEach(function (entry) {
                if (entry.ratingCount > max) {
                    max = entry.ratingCount;
                }
            });
            this.maxRatingCount = max;
        }
    }

    calcMaxRatingValue() {
        if (this.filteredMovies) {
            let max:number = 0;
            this.filteredMovies.forEach(function (entry) {
                let n:number = parseFloat(entry.ratingValue);
                if (n > max) {
                    max = n;
                }
            });
            this.maxRatingValue = max;
        }
    }

    calcMaxYear() {
        if (this.filteredMovies) {
            let maxY:number = 0;
            this.filteredMovies.forEach(function (entry) {
                // MAX
                if (entry.year > maxY) {
                    maxY = entry.year;
                }
            });
            this.maxYear = maxY;
        }
    }

    calcMinYear() {
        if (this.allMovies) {
            let minY:number = 10000;
            this.allMovies.forEach(function (entry) {
                // MIN
                if ((entry.year > 1900) && (entry.year < minY)) {
                    minY = entry.year;
                }
            });
            this.minYear = minY;
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
    }

    onlineChanged(isOnline:boolean) {
        if (isOnline && this.shouldLoadMoviesFromServer) {
            this.registerForWebRequest();
        }
    }

    preLoadMoviesFromLocalStorage() {
        if (localStorage && localStorage.movies
            && (localStorage.movies != "undefined")) {
                this.allMovies = JSON.parse(localStorage.movies);
                if (this.allMovies) {
                    this.filterAndSetMovies();
                    this.calcMinYear();
                    this.calcMinMaxValues();
                }
        }
    }

    storeMoviesToLocalStorage() {
        if (localStorage) {
            localStorage.movies = JSON.stringify(this.allMovies);
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
        this.allMovies = movies as any as Movie[];
        this.filterAndSetMovies(true);
        this.storeMoviesToLocalStorage();
        this.shouldLoadMoviesFromServer = false;
        this.calcMinYear();
        this.calcMinMaxValues();
    }

    shouldShowProgressBar() {
        if (navigator.onLine) {
            return this.shouldLoadMoviesFromServer || this.isParentLoading;
        } else {
            return false;
        }
    }

    private showSnackbar(message:string, sliderValue: number, toLocale:boolean) {
        let valueToShow = (toLocale ? sliderValue.toLocaleString('en') : sliderValue.toString());
        this.snackBar.open(message + " " + valueToShow, '', {
            duration: 1000,
        });
    }

    public filterAndSetMovies(incrementally?:boolean) {
        let filter:string = this.searchString.toUpperCase().trim();
        this.filteredMovies =
            this.allMovies.filter(
                movie =>
                    (
                        (movie.ratingCount >= this.minRatingCountFilter) &&
                        ((parseFloat(movie.ratingValue) * 10) >= (this.minRatingValueFilter * 10)) &&
                        (movie.year >= this.minYearValueFilter) &&
                        (movie.movie.toUpperCase().indexOf(filter) > -1)
                    )
            );

        // iPhone fix: fixes issues with focused input field while onscreen keyboard is visible
        if (this.filteredMovies.length < 60) {
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

        for (let i = 0; i < this.displayedMovies.length; i++) {
            // try to find old elemt-i in new array
            let movie:Movie = this.filteredMovies.find(element => element.movie === this.displayedMovies[i].movie);
            if (movie) {
                indicesOfFoundDisplayedMovies.push(this.filteredMovies.indexOf(movie));
            } else {
                // remove movie
                this.displayedMovies.slice(i, 1);
            }
        }
        // add movies to displayedMovies, if they are new in the filtered list
        let min:number = Math.min(100, this.filteredMovies.length);
        for (let i = 0; i < min; i++) {
            if (indicesOfFoundDisplayedMovies.indexOf(i) == -1) {
                this.displayedMovies.push(this.filteredMovies[i]);
            }
        }
    }

    setDisplayedMovies() {
        this.displayedMovies = [];

        let min:number = Math.min(100, this.filteredMovies.length);

        for (let i = 0; i < min; i++) {
            this.displayedMovies.push(this.filteredMovies[i]);
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
            this.filteredMovies = this.filteredMovies.sort((a, b) => {
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
        this.minRatingCountFilter = newValue;
        this.showSnackbar("Minimum Rating Count set to:",
            this.minRatingCountFilter, true);
        this.filterAndSetMovies();
    }

    onRatingValueChanged(newValue:number) {
        this.minRatingValueFilter = newValue;
        this.showSnackbar("Minimum Rating Value set to:",
            this.minRatingValueFilter, true);
        this.filterAndSetMovies();
    }

    onYearChanged(newValue:number) {
        this.minYearValueFilter = newValue;
        this.showSnackbar("Minimum Year set to:",
            this.minYearValueFilter, false);
        this.filterAndSetMovies();
    }
}

function compare(a, b, isAsc) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
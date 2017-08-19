import {Component, Input, OnInit} from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";
import { MdSnackBar } from "@angular/material";
import { IsOnlineService } from "../services/is-online.service";
import { animate, state, style, transition, trigger } from "@angular/animations";
import {DialogSettingsService} from "../dialog-settings/dialog-settings.service";

@Component({
    selector: 'app-movies',
    templateUrl: './videos.component.html',
    styleUrls: ['./videos.component.css'],
    animations: [
        /* FADE IN_OUT */
        trigger('optionsTrigger', [
            state('in', style({
                    opacity: 1,
                    transform: 'translateY(0)'
            })),
            transition('void => *', [
                // specify starting style for animation
                style({
                    opacity: 0,
                    transform: 'translateY(-100%)'
                }),
                animate(300)
            ]),
            transition('* => void', [
                // specify end state after animation
                animate(300,
                    style({
                        opacity: 0,
                        transform: 'translateY(-100%)'
                    })
                )
            ])
        ]),
    ]
})

// it is possible to animate the transition between 2 states, just use animate()
// for animating from void, specify the starting state for animation
// it is also possible to specify the end state when animation finishes
// ... after that, animation transitions immediately to the target state?

export class VideosComponent implements OnInit {
    @Input() isParentLoading:boolean = true;

    displayedMovies: Movie[] = [];
    filteredMovies: Movie[];
    allMovies: Movie[];

    //TODO: calc this dynamically
    //TODO: minEntries should correspond with scrollDistance?
    minEntries:number = 48;
    displayedEntries:number = this.minEntries;

    shouldLoadMoviesFromServer:boolean = true;

    maxRatingCount: number;
    maxYear: number;
    minYear: number;

    minRatingCountFilter:number = 10000;
    minRatingValueFilter:number = 6;
    maxYearValueFilter:number = 2000;
    minYearValueFilter:number = 2000;

    searchString:string =" ";

    //TODO: remove after debugging
    scrolls:number = 0;

    calcMaxRatingCount() {
        if (this.allMovies) {
            let max:number = 0;
            this.allMovies.forEach(function (entry) {
                if (entry.ratingCount > max) {
                    max = entry.ratingCount;
                }
            });
            this.maxRatingCount = max;
        }
    }

    calcMinMaxYear() {
        if (this.allMovies) {
            let maxY:number = 0;
            let minY:number = 10000;
            this.allMovies.forEach(function (entry) {
                // MAX
                if (entry.year > maxY) {
                    maxY = entry.year;
                }
                // MIN
                if ((entry.year > 1900) && (entry.year < minY)) {
                    minY = entry.year;
                    console.log(entry.year + " , " + entry.movie);
                }
            });
            console.log("Year: " + minY + " , " + maxY);
            this.maxYear = maxY;
            this.minYear = minY;
        }
    }

    calcMinMaxValues() {
        this.calcMaxRatingCount();
        this.calcMinMaxYear();
    }

    constructor(private webService: WebService,
                private isOnlineService: IsOnlineService,
                public snackBar: MdSnackBar,
                public dialogSettingsService: DialogSettingsService) {
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
                    this.filterMovies();
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
        this.filterMovies();
        this.storeMoviesToLocalStorage();
        this.shouldLoadMoviesFromServer = false;
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

    public filterMovies() {
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
        if (this.filteredMovies.length < this.displayedEntries) {
            // the filter is very narrow
            this.displayedEntries = this.filteredMovies.length;
        } else if (this.displayedEntries < this.minEntries) {
            //TODO: revise this condition
            this.displayedEntries = Math.min(this.filteredMovies.length, this.minEntries);
        }
        this.setDisplayedMovies();
    }

    onRatingCountChanged(newValue:number) {
        this.minRatingCountFilter = newValue;

        this.showSnackbar("Minimum Rating Count set to:", this.minRatingCountFilter, true);

        this.filterMovies();
    }

    onRatingValueChanged(newValue:number) {
        this.minRatingValueFilter = newValue;

        this.showSnackbar("Minimum Rating Value set to:", this.minRatingValueFilter, true);

        this.filterMovies();
    }

    onYearChanged(newValue:number) {
        this.minYearValueFilter = newValue;

        this.showSnackbar("Minimum Year set to:", this.minYearValueFilter, false);

        this.filterMovies();
    }

    openRatingValueDialog() {
        this.dialogSettingsService
            .openRatingValueDialog(this.minRatingValueFilter)
            .subscribe(newValue => this.onRatingValueChanged(newValue));
    }

    openRatingCountDialog() {
        this.dialogSettingsService
            .openRatingCountDialog(this.minRatingCountFilter, this.maxRatingCount)
            .subscribe(newValue => this.onRatingCountChanged(newValue));
    }

    openYearDialog() {
        this.dialogSettingsService
            .openYearDialog(this.minYearValueFilter, this.minYear, this.maxYear)
            .subscribe(newValue => this.onYearChanged(newValue));
    }

    onScrollDown () {
        console.log('scrolled down!!')
        this.addDisplayedMovies();
        this.scrolls += 1;
    }

    onScrollUp () {
        console.log('scrolled up!!')
        this.removeDisplayedMovies();
        this.scrolls -= 1;
    }

    setDisplayedMovies() {
        this.displayedMovies = [];

        for (let i = 0; i < this.displayedEntries; i++) {
            this.displayedMovies.push(this.filteredMovies[i]);
        }
        this.displayedEntries = this.displayedMovies.length;
    }

    addDisplayedMovies() {
        //TODO: remove from beginning instead of adding only
        let target:number = this.displayedEntries + 2 * this.minEntries;
        let max:number;
        max = Math.min(target, this.filteredMovies.length);

        console.log("add" + max);

        for (let i = this.displayedEntries; i < max; i++) {
            this.displayedMovies.push(this.filteredMovies[i]);
        }
        //this.displayedEntries += 2 * this.minEntries;
        this.displayedEntries = this.displayedMovies.length;
    }

    removeDisplayedMovies() {
        //TODO: add on top
        /*for (let i = 0; i < this.minEntries; i++) {
            this.displayedMovies.pop();
        }
        this.displayedEntries -= this.minEntries;*/


        //TODO: fix this workaround
        this.displayedEntries = this.minEntries;
        this.setDisplayedMovies();
        this.displayedEntries = this.displayedMovies.length;
    }
}
import {Component, HostListener, Input, OnInit} from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";
import { MdSnackBar } from "@angular/material";
import { IsOnlineService } from "../services/is-online.service";
import { animate, keyframes, state, style, transition, trigger } from "@angular/animations";
import { DialogSettingsService } from "../dialog-settings/dialog-settings.service";

@Component({
    selector: 'app-movies',
    templateUrl: './videos.component.html',
    styleUrls: ['./videos.component.css'],
    animations: [
        /* FADE IN_OUT */
        trigger('fadeInOutTrigger', [
            state('in', style({
                    opacity: 1,
                    transform: 'translateY(0)'
            })),
            transition('void => in', [
                // specify starting style for animation
                style({
                    opacity: 0,
                    transform: 'translateY(-100%)'
                }),
                animate(300)
            ]),
            transition('in => void', [
                // specify end state after animation
                animate(300,
                    style({
                        opacity: 0,
                        transform: 'translateY(-100%)'
                    })
                )
            ])
        ]),
        /* SCALE IN_OUT SLOW */
        trigger('scalingTrigger', [
            state('normal1', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal2', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal1 <=> normal2', [
                animate(300, keyframes([
                    style({opacity: 1, transform: 'scale(1)', offset: 0}),
                    style({opacity: 0.5, transform: 'scale(3)', offset: 0.2}),
                    style({opacity: 1, transform: 'scale(1)', offset: 1}),
                ]))
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

    shouldLoadMoviesFromServer:boolean = true;

    maxRatingCount: number;
    maxRatingValue: number;
    maxYear: number;
    minYear: number;

    minRatingCountFilter:number = 10000;
    minRatingValueFilter:number = 6;
    //TODO: let user set this accordingly
    maxYearValueFilter:number = 2000;
    minYearValueFilter:number = 2000;

    searchString:string ="";

    animationYearScalingState:string = "normal1";
    animationRatingValueScalingState:string = "normal1";
    animationRatingCountScalingState:string = "normal1";
    conditionalAnimation:string = "in";

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
        if (incrementally) {
            this.setDisplayedMoviesIncrementally();
        } else {
            this.setDisplayedMovies();
        }
        this.calcMinMaxValues();
    }

    onRatingCountChanged(newValue:number) {
        this.minRatingCountFilter = newValue;

        this.showSnackbar("Minimum Rating Count set to:", this.minRatingCountFilter, true);

        this.filterAndSetMovies();
    }

    onRatingValueChanged(newValue:number) {
        this.minRatingValueFilter = newValue;

        this.showSnackbar("Minimum Rating Value set to:", this.minRatingValueFilter, true);

        this.filterAndSetMovies();
    }

    onYearChanged(newValue:number) {
        this.minYearValueFilter = newValue;

        this.showSnackbar("Minimum Year set to:", this.minYearValueFilter, false);

        this.filterAndSetMovies();
    }

    onAllChanged(newValue:number) {
        this.minYearValueFilter = newValue;

        this.showSnackbar("Minimum Year set to:", this.minYearValueFilter, false);

        this.filterAndSetMovies();
    }

    openRatingValueDialog() {
        this.animateRatingValueScalingTrigger();
        this.dialogSettingsService
            .openRatingValueDialog(this.minRatingValueFilter, this.maxRatingValue)
            .subscribe(newValue => this.onRatingValueChanged(newValue));
    }

    openRatingCountDialog() {
        this.animateRatingCountScalingTrigger();
        this.dialogSettingsService
            .openRatingCountDialog(this.minRatingCountFilter, this.maxRatingCount)
            .subscribe(newValue => this.onRatingCountChanged(newValue));
    }

    openYearDialog() {
        this.animateYearScalingTrigger();
        this.dialogSettingsService
            .openYearDialog(this.minYearValueFilter, this.minYear, this.maxYear)
            .subscribe(newValue => this.onYearChanged(newValue));
    }

    openAllSettingsDialog() {
        this.animateYearScalingTrigger();
        this.dialogSettingsService
            .openAllDialog(this.minYearValueFilter, this.minYear, this.maxYear,
                this.minRatingCountFilter, this.maxRatingCount, this.minRatingValueFilter,
                this.maxRatingValue)
            .subscribe(newValue => this.onAllChanged(newValue));
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

    animateYearScalingTrigger() {
        this.animationYearScalingState = (this.animationYearScalingState === "normal1" ? this.animationYearScalingState = "normal2" : this.animationYearScalingState = "normal1");
    }

    animateRatingValueScalingTrigger() {
        this.animationRatingValueScalingState = (this.animationRatingValueScalingState === "normal1" ? this.animationRatingValueScalingState = "normal2" : this.animationRatingValueScalingState = "normal1");
    }

    animateRatingCountScalingTrigger() {
        this.animationRatingCountScalingState = (this.animationRatingCountScalingState === "normal1" ? this.animationRatingCountScalingState = "normal2" : this.animationRatingCountScalingState = "normal1");
    }


    /* SCROLL TO TOP */
    shouldShowScrollToTop: boolean = false;
    animateButtonEntryState: string = "in";

    scrollToTop() {
        window.scrollTo(0, 0);
    }

    @HostListener('window:scroll', ['$event'])
    doSomething(event) {
        this.shouldShowScrollToTop = (window.pageYOffset >= window.screen.height/2);
    }
}
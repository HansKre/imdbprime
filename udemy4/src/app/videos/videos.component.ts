import {Component, Input, OnInit} from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";
import { MdSliderChange, MdSnackBar } from "@angular/material";
import { IsOnlineService } from "../services/is-online.service";
import { animate, state, style, transition, trigger } from "@angular/animations";
import {DialogRatingValueService} from "../dialog-rating-value/dialog-rating-value.service";

@Component({
    selector: 'app-movies',
    templateUrl: './videos.component.html',
    styleUrls: ['./videos.component.css'],
    animations: [
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
    minEntries:number = 24;
    displayedEntries:number = this.minEntries;

    maxRatingCount: number = 0;
    //TODO: maxValue
    //TODO: min/max Year

    shouldLoad:boolean = true;

    showOptions:boolean = false;

    ratingCountSliderValue:number = 10000;
    ratingValueSliderValue:number = 6;
    yearSliderValue:number = 1950;

    searchString:string ="";

    setMaxRatingCount() {
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

    constructor(private webService: WebService,
                private isOnlineService: IsOnlineService,
                public snackBar: MdSnackBar,
                public dialogRatingValueService: DialogRatingValueService) {
    }

    ngOnInit() {
        this.isOnlineService.isOnlineObserveable()
            .subscribe((isOnline:boolean) => this.onlineChanged(isOnline));
        this.preLoadMoviesFromLocalStorage();
        this.registerForWebRequest();
    }

    onlineChanged(isOnline:boolean) {
        if (isOnline && this.shouldLoad) {
            this.registerForWebRequest();
        }
    }

    preLoadMoviesFromLocalStorage() {
        if (localStorage && localStorage.movies
            && (localStorage.movies != "undefined")) {
                this.allMovies = JSON.parse(localStorage.movies);
                if (this.allMovies) {
                    this.filterMovies();
                    this.setMaxRatingCount();
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
        this.shouldLoad = false;
        this.setMaxRatingCount();
    }

    shouldShowProgressBar() {
        if (navigator.onLine) {
            return this.shouldLoad || this.isParentLoading;
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
        let filter:string = this.searchString.toUpperCase();
        this.filteredMovies =
            this.allMovies.filter(
                movie =>
                    (
                        (movie.ratingCount >= this.ratingCountSliderValue) &&
                        ((parseFloat(movie.ratingValue) * 10) >= (this.ratingValueSliderValue * 10)) &&
                        (movie.year >= this.yearSliderValue) &&
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
        console.log(filter + " ");
        this.setDisplayedMovies();
    }

    mdSliderInput_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;

        this.showSnackbar("Minimum Rating Count set to:", this.ratingCountSliderValue, true);

        this.filterMovies();
    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {
        this.showSnackbar("Minimum Rating Count set to:", changeEvent.value, true);
    }

    mdSliderInput_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;

        this.showSnackbar("Minimum Rating Value set to:", this.ratingValueSliderValue, true);

        this.filterMovies();
    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {
        this.showSnackbar("Minimum Rating Value set to:", changeEvent.value, true);
    }

    mdSliderInput_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;

        this.showSnackbar("Minimum Year set to:", this.yearSliderValue, false);

        this.filterMovies();
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {
        this.showSnackbar("Minimum Year set to:", changeEvent.value, false);
    }

    openDialog() {
        this.dialogRatingValueService
            .confirm('Confirm Dialog', 'Foo Bar')
            .subscribe(res => console.log(res));
    }

    onScrollDown () {
        console.log('scrolled down!!')
        this.addDisplayedMovies();
    }

    onScrollUp () {
        console.log('scrolled up!!')
        this.removeDisplayedMovies();
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

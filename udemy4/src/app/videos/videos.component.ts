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

    displayedMovies: Movie[];
    allMovies: Movie[];
    maxRatingCount: number = 0;

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
            localStorage.movies = JSON.stringify(this.displayedMovies);
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
        this.displayedMovies =
            this.allMovies.filter(
                movie =>
                    (
                        (movie.ratingCount >= this.ratingCountSliderValue) &&
                        ((parseFloat(movie.ratingValue) * 10) >= (this.ratingValueSliderValue * 10)) &&
                        (movie.year >= this.yearSliderValue) &&
                        (movie.movie.toUpperCase().indexOf(filter) > -1)
                    )
            );
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
    }

    onScrollUp () {
        console.log('scrolled up!!')
    }

}

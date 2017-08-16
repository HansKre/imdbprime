import {Component, Input, OnInit} from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";
import {MdSliderChange, MdSnackBar} from "@angular/material";
import {Direction} from "@angular/cdk";

@Component({
  selector: 'app-server',
  templateUrl: './videos.component.html',
  styleUrls: ['./videos.component.css']
})
export class VideosComponent implements OnInit {
    @Input() isParentLoading:boolean = true;

    displayedMovies: Movie[];
    allMovies: Movie[];
    maxRatingCount: number = 0;

    shouldLoad:boolean = true;
    onlineString:string;

    showOptions:boolean = false;

    ratingCountSliderValue:number = 10000;
    ratingValueSliderValue:number = 0;

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
                public snackBar: MdSnackBar) {
      //setTimeout(() => {this.allowNewServer = true}, 2000);
    }

    ngOnInit() {
        this.setInitialOnlineStatus();
        window.addEventListener('online', this.onOnline.bind(this));
        window.addEventListener( 'offline', this.onOffline.bind(this));
        this.preLoadMoviesFromLocalStorage();
        this.registerForWebRequest();
        //TODO: wird 2x aufgerufen?
        //TODO: muss das promise neu initiiert werden, wenn es beim ersten Mal gescheitert ist?
    }

    setInitialOnlineStatus() {
        this.onlineString = navigator.onLine ? "online" : "offline";

    }

    onOnline() {
        this.onlineString = "online";
        if (this.shouldLoad) {
            this.registerForWebRequest();
        }
    }

    onOffline() {
        this.onlineString = "offline";
    }

    preLoadMoviesFromLocalStorage() {
        if (localStorage && localStorage.movies
            && (localStorage.movies != "undefined")) {
                this.displayedMovies = JSON.parse(localStorage.movies);
                if (this.displayedMovies) {
                    this.allMovies = this.displayedMovies;
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
        this.displayedMovies = movies as any as Movie[];
        this.allMovies = this.displayedMovies;
        this.storeMoviesToLocalStorage();
        this.shouldLoad = false;
        this.setMaxRatingCount();
    }

    shouldShowProgressBar() {
        if (navigator.onLine) {
            console.log("parent: " + (this.isParentLoading ? "loading" : "not loading"));
            console.log("self: " + (this.shouldLoad ? "loading" : "not loading"));
            return this.shouldLoad || this.isParentLoading;
        } else {
            return false;
        }
    }

    private showSnackbar(message:string, sliderValue: number) {
        this.snackBar.open(message + " " + sliderValue.toLocaleString('en'), '', {
            duration: 1000,
        });
    }

    mdSliderInput_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;
        console.log("value: " + this.ratingValueSliderValue + " count: " + this.ratingCountSliderValue);

        this.showSnackbar("Minimum Rating Count set to:", this.ratingCountSliderValue);

        this.displayedMovies =
            this.allMovies.filter(
                movie =>
                (
                    (movie.ratingCount > this.ratingCountSliderValue) &&
                    ((parseFloat(movie.ratingValue) * 10) > (this.ratingValueSliderValue * 10))
                )
            );
    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {
        this.showSnackbar("Minimum Rating Count set to:", changeEvent.value);
    }

    mdSliderInput_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
        console.log("value: " + this.ratingValueSliderValue + " count: " + this.ratingCountSliderValue);

        if (this.ratingValueSliderValue === 6) {
            this.displayedMovies = this.allMovies;
        } else {
            this.displayedMovies =
                this.allMovies.filter(
                    movie =>
                        (
                            (movie.ratingCount > this.ratingCountSliderValue) &&
                            ((parseFloat(movie.ratingValue) * 10) > (this.ratingValueSliderValue * 10))
                        )
                );

        }
    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {
        this.showSnackbar("Minimum Rating Value set to:", changeEvent.value);
    }
}

import { Component, OnInit } from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";

@Component({
  selector: 'app-server',
  templateUrl: './videos.component.html',
  styleUrls: ['./videos.component.css']
})
export class VideosComponent implements OnInit {

    autoTicks = false;
    disabled = false;
    invert = false;
    max = 100;
    min = 0;
    showTicks = false;
    step = 1;
    thumbLabel = false;
    value = 0;
    vertical = false;



    allowNewServer: boolean = false;
    displayedMovies: Movie[];
    allMovies: Movie[];
    updatedMovies = false;
    sliderVal: number = 0;
    maxRatingCount: number = 0;

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

    constructor(private webService: WebService) {
      setTimeout(() => {this.allowNewServer = true}, 2000);
    }

    ngOnInit() {
        this.preLoadMoviesFromLocalStorage();
        this.registerForWebRequest();
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
            console.log("Promise rejected:" + error.toString());
        }.bind(this));
    }

    private resolvePromisedRequest(movies) {
        this.displayedMovies = movies as any as Movie[];
        this.allMovies = this.displayedMovies;
        this.storeMoviesToLocalStorage();
        this.updatedMovies = true;
        this.setMaxRatingCount();
    }

    wasOnline() {
        return this.updatedMovies;
    }

    sliderChanged(sliderValue: number) {
        let newDisplayedMovies: Movie[] = [];
        this.allMovies.forEach(function (entry) {
            if (entry.ratingCount > sliderValue) {
                newDisplayedMovies.push(entry);
            }
        });
        this.displayedMovies = newDisplayedMovies;
    }
}

import { Component, OnInit } from '@angular/core';
import { WebService } from "../services/web.service";
import { Movie } from "../structures/movie";

@Component({
  selector: 'app-server',
  templateUrl: './videos.component.html',
  styleUrls: ['./videos.component.css']
})
export class VideosComponent implements OnInit {
    allowNewServer: boolean = false;
    displayedMovies: Movie[];
    allMovies: Movie[];
    updatedMovies = false;
    sliderVal: number = 0;
    maxRatingCount: number = 0;

    getMaxRatingCount() {
        if (this.maxRatingCount == 0) {
            let max = 0;
            this.allMovies.forEach(function (entry) {
                if (entry.ratingCount > max) {
                    max = entry.ratingCount;
                }
            });
            this.maxRatingCount = max;
        }
        return this.maxRatingCount;
    }

    constructor(private webService: WebService) {
      setTimeout(() => {this.allowNewServer = true}, 2000);
    }

    ngOnInit() {
        this.preLoadMoviesFromLocalStorage();
        this.registerForWebRequest();
    }

    preLoadMoviesFromLocalStorage() {
        if (localStorage) {
            if (localStorage.movies) {
                this.displayedMovies = JSON.parse(localStorage.movies);
                this.allMovies = this.displayedMovies;
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
            this.displayedMovies = movies as any as Movie[];
            this.allMovies = movies as any as Movie[];
            this.storeMoviesToLocalStorage();
            this.updatedMovies = true;
        }.bind(this), function (error) {
            alert("Movies could not be retrieved from the web service.");
            console.log(error);
        }.bind(this));
    }

    /*useLocalStorage() {
        if (!localStorage.pageLoadCount) {
            localStorage.pageLoadCount = 0;
        }
        localStorage.pageLoadCount = parseInt(localStorage.pageLoadCount) + 1;
        alert(localStorage.pageLoadCount);
    }*/

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

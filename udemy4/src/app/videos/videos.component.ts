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
    movies: Movie[];
    updatedMovies = false;

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
                this.movies = JSON.parse(localStorage.movies);
            }
        }
    }

    storeMoviesToLocalStorage() {
        if (localStorage) {
            localStorage.movies = JSON.stringify(this.movies);
        }
    }

    registerForWebRequest() {
        this.webService.getPromise().then(function (movies) {
            this.movies = movies as any as Movie[];
            this.storeMoviesToLocalStorage();
            this.updatedMovies = true;
        }.bind(this), function (error) {
            alert("Movies could not be retrieved from the web service.");
            console.log(error);
        }.bind(this));
    }

    useLocalStorage() {
        if (!localStorage.pageLoadCount) {
            localStorage.pageLoadCount = 0;
        }
        localStorage.pageLoadCount = parseInt(localStorage.pageLoadCount) + 1;
        alert(localStorage.pageLoadCount);
    }

    wasOnline() {
        return this.updatedMovies;
    }
}

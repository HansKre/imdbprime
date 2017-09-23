import { Injectable } from '@angular/core';
import { Http } from "@angular/http";
import { Movie } from "../structures/movie";

@Injectable()
export class WebService {

    movies: Movie[];

    //private url = ''http://imdbprime-snah.rhcloud.com/getMoviesWithRatings.php?sortBy=ratingValue&order=descending&ratingCountMin=10000'';
    private url = 'http://imdbprime.herokuapp.com/php/webAPI/getMoviesWithRatings.php?sortBy=ratingValue&order=descending&ratingCountMin=10000';

    constructor(private http: Http) { }

    getObservable() {
        return this.http.get(this.url);
    }

    getPromise() {
        return new Promise(function (resolve, reject) {
            this.http.get(this.url)
                .subscribe(
                    response => this.handleResponse(response),
                    error => reject(error),
                    () => resolve(this.movies)
                );
        }.bind(this));
    }

    handleResponse(response: any) {
      let responseJson: any;
      let correctType = true;

      responseJson =response.json();
      if (responseJson instanceof Array) {
          responseJson.forEach(function (entry) {
              if (
                  (entry.movie === undefined) ||
                  (entry.year === undefined) ||
                  (entry.imdbMovieUrl === undefined) ||
                  (entry.director === undefined) ||
                  (entry.ratingValue === undefined) ||
                  (entry.ratingCount === undefined) ||
                  (entry.ratingCountString === undefined)
              ) {
                  console.log("Unknown type: " + entry.valueOf());
                  correctType = false;
              }
          })
      } else {
          correctType = false;
          console.log("Not an array");
      }
      if (correctType) {
          this.movies = <Movie[]>responseJson;
          /*for(let movie of movies) {
              console.log(movie.movie + " " + movie.year + " " + movie.director);
          }*/
      }
    }
}

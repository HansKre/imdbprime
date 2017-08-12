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

    constructor(private webService: WebService) {
      setTimeout(() => {this.allowNewServer = true}, 2000);
    }

    ngOnInit() {
        this.webService.getPromise().then(function (movies) {
            this.movies = movies as any as Movie[];
        }.bind(this), function (error) {
            console.log(error);
        }.bind(this));
    }

    sendGetRequest() {
      console.log("do something");
    }

    refresh() {
        console.log("Refresh: " + this.movies);
    }

}

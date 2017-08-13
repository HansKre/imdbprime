import {Component, OnInit} from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{

    ngOnInit(): void {
        this.swapApplicationCache();
    }

    private swapApplicationCache() {
        if (window.applicationCache.status === window.applicationCache.UPDATEREADY) {
            window.applicationCache.swapCache();
        }
    }
}

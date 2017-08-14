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
            // replace the current cache with the newer
            window.applicationCache.swapCache();
            // page content needs to be reloaded after this
            window.location.reload();
        }
    }
}

import {Component, OnInit} from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
    isCacheLoading:boolean = false;

    ngOnInit(): void {
        this.swapApplicationCache();
    }

    swapCache() {
        console.log("onupdateready fired");
        this.isCacheLoading = true;
        // replace the current cache with the newer
        window.applicationCache.swapCache();
        // page content needs to be reloaded after this
        window.location.reload();
        this.isCacheLoading = false;
    }

    private swapApplicationCache() {
        // register for UPDATEREADY event
        window.applicationCache.onupdateready = function() {
            console.log("onupdateready fired");
            this.swapCache();
        }.bind(this);

        // swapcache if already in that state
        if (window.applicationCache.status === window.applicationCache.UPDATEREADY) {
            this.swapCache();
        }
    }
}

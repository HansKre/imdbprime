import {Component, OnInit} from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent implements OnInit{
    isCacheLoading:boolean = false;

    ngOnInit(): void {
        // swapcache if already in UPDATEREADY state
        if (window.applicationCache.status === window.applicationCache.UPDATEREADY) {
            this.swapCacheAndReloadContent();
        }

        this.registerApplicationCacheEvents();
    }

    private registerApplicationCacheEvents() {
        this.registerForDOWNLOADING();
        this.registerForUPDEATEREADY();
    }

    private registerForDOWNLOADING() {
        window.applicationCache.ondownloading = function () {
            this.isCacheLoading = true;
        }.bind(this);
    }

    private registerForUPDEATEREADY() {
        window.applicationCache.onupdateready = function () {
            this.swapCacheAndReloadContent();
        }.bind(this);
    }

    swapCacheAndReloadContent() {
        this.isCacheLoading = true;

        // replace the current cache with the newer
        window.applicationCache.swapCache();

        // page content needs to be reloaded after this
        window.location.reload();

        this.isCacheLoading = false;
    }
}

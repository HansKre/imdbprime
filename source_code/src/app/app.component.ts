import {Component, OnInit} from '@angular/core';
import {Movie} from "./structures/movie";

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
        this.registerForCACHED();
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

    // cached - The resources listed in the manifest have been fully downloaded
    // and the application is now cached locally.
    private registerForCACHED() {
        window.applicationCache.oncached = function () {
            this.isCacheLoading = false;
        }.bind(this);
    }

    swapCacheAndReloadContent() {
        this.isCacheLoading = true;

        // replace the current cache with the newer
        window.applicationCache.swapCache();

        // page content needs to be reloaded after this
        window.location.reload();

        // code past this point won't be executed
    }
}

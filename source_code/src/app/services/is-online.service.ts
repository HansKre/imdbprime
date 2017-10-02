import { Injectable } from '@angular/core';
import {Observable} from "rxjs/Observable";
import {Subject} from "rxjs/Subject";


@Injectable()
export class IsOnlineService {

    private _isOnline: boolean = false;
    // use the Subject-object as an intermediary step to create an Observeable to observe value changes
    private isOnlineSubject: Subject<boolean>;
    private isOnline$: Observable<boolean>;

    constructor() {
        this.isOnlineSubject = new Subject<boolean>();
        this.isOnline$ = this.isOnlineSubject.asObservable();

        this.isOnline = navigator.onLine;

        window.addEventListener('online', this.onOnline.bind(this));
        window.addEventListener( 'offline', this.onOffline.bind(this));
    }

    set isOnline(value: boolean) {
        this._isOnline = value;
        this.isOnlineSubject.next(value);
    }

    get isOnline(): boolean {
        return this._isOnline;
    }

    onOnline() {
        console.log("online");
        this.isOnline = true;
    }

    onOffline() {
        console.log("offline");
        this.isOnline = false;
    }

    /*
    to subscribe:
    this.isOnlineService.isOnlineObserveable()
            .subscribe((isOnline:boolean) => this.onlineChanged(isOnline));
     */
    isOnlineObserveable() {
        return this.isOnline$;
    }

}

import { Injectable, OnInit } from '@angular/core';
import {Observable} from "rxjs/Observable";
import {Subject} from "rxjs/Subject";


@Injectable()
export class IsOnlineService implements OnInit {

    private _isOnline: boolean = false;
    private isOnlineSubject: Subject<boolean>;
    private isOnline$: Observable<boolean>;

    constructor() {
        this.isOnlineSubject = new Subject<boolean>();
        this.isOnline$ = this.isOnlineSubject.asObservable();
    }

    set isOnline(value: boolean) {
        this._isOnline = value;
        this.isOnlineSubject.next(value);
    }

    get isOnline(): boolean {
        return this._isOnline;
    }

    onOnline() {
        this.isOnline = true;
    }

    onOffline() {
        this.isOnline = false;
    }

    ngOnInit(): void {
        window.addEventListener('online', this.onOnline.bind(this));
        window.addEventListener( 'offline', this.onOffline.bind(this));
    }

    isOnlineObserveable() {
        return this.isOnline$;
    }

}

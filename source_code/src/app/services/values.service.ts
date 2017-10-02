import { Injectable } from '@angular/core';
import {Movie} from "../structures/movie";
import {Subject} from "rxjs/Subject";
import {Observable} from "rxjs/Observable";

@Injectable()
export class ValuesService {

    displayedMovies: Movie[] = [];
    filteredMovies: Movie[];
    allMovies: Movie[];

    shouldLoadMoviesFromServer:boolean = true;

    searchString:string = "";

    maxRatingCount: number = 0;
    maxRatingValue: number = 0;
    maxYear: number = 0;
    minYear: number = 0;

    /*
    *  The following fields shall be observeable for changes.
    *  The Subject-object is used to create the matching Observable-object.
    *  Also see is-inline.service.ts to see another example.
    */
    private _minRatingCountFilter:number = 10000;
    private _minRatingValueFilter:number = 6;
    private _minYearValueFilter:number = 2000;

    private minRatingCountFilterSubject: Subject<number>;
    private minRatingValueFilterSubject: Subject<number>;
    private minYearValueFilterSubject: Subject<number>;

    private _minRatingCountFilterObservable: Observable<number>;
    private _minRatingValueFilterObservable: Observable<number>;
    private _minYearValueFilterObservable: Observable<number>;

    constructor() {
        this.minRatingCountFilterSubject = new Subject<number>();
        this.minRatingValueFilterSubject = new Subject<number>();
        this.minYearValueFilterSubject = new Subject<number>();

        this._minRatingCountFilterObservable = this.minRatingCountFilterSubject.asObservable();
        this._minRatingValueFilterObservable = this.minRatingValueFilterSubject.asObservable();
        this._minYearValueFilterObservable = this.minYearValueFilterSubject.asObservable();
    }

    get minRatingCountFilter(): number {
        return this._minRatingCountFilter;
    }

    set minRatingCountFilter(value: number) {
        this._minRatingCountFilter = value;
        this.minRatingCountFilterSubject.next(value);
    }

    get minRatingValueFilter(): number {
        return this._minRatingValueFilter;
    }

    set minRatingValueFilter(value: number) {
        this._minRatingValueFilter = value;
        this.minRatingValueFilterSubject.next(value);
    }

    get minYearValueFilter(): number {
        return this._minYearValueFilter;
    }

    set minYearValueFilter(value: number) {
        this._minYearValueFilter = value;
        this.minYearValueFilterSubject.next(value);
    }


    get minRatingCountFilterObservable(): Observable<number> {
        return this._minRatingCountFilterObservable;
    }

    get minRatingValueFilterObservable(): Observable<number> {
        return this._minRatingValueFilterObservable;
    }

    get minYearValueFilterObservable(): Observable<number> {
        return this._minYearValueFilterObservable;
    }
}

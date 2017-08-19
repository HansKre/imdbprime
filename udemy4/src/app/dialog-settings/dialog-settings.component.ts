import { Component } from '@angular/core';
import {MdDialogRef, MdSliderChange} from "@angular/material";
import {Subject} from "rxjs/Subject";
import {Observable} from "rxjs/Observable";

@Component({
  selector: 'dialog-settings-component',
  templateUrl: './dialog-settings.component.html',
  styleUrls: ['./dialog-settings.component.css']
})
export class DialogSettingsComponent {

    public title: string;
    public message: string;

    constructor(public dialogRef: MdDialogRef<DialogSettingsComponent>) {
        this.ratingValueSliderValueObserveable = this.ratingValueSliderValueSubject.asObservable();
        this.ratingCountSliderValueObserveable = this.ratingCountSliderValueSubject.asObservable();
        this.yearSliderValueObserveable = this.yearSliderValueSubject.asObservable();
    }

    /* RATING VALUE */
    ratingValueShow:boolean = false;
    _ratingValueSliderValue:number;
    ratingValueSliderValueSubject:Subject<number> = new Subject<number>();
    ratingValueSliderValueObserveable:Observable<number>;

    set ratingValueSliderValue(value: number) {
        this._ratingValueSliderValue = value;
        this.ratingValueSliderValueSubject.next(value);
    }

    get ratingValueSliderValue () {
        return this._ratingValueSliderValue;
    }

    public ratingValueObserveable():Observable<number> {
        return this.ratingValueSliderValueObserveable;
    }

    public openRatingValueDialog(initWith:number) {
        this.ratingValueSliderValue = initWith;
        this.ratingValueShow = true;
    }

    mdSliderInput_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
    }

    /* RATING COUNT*/
    ratingCountShow:boolean = false;
    _ratingCountSliderValue:number;
    ratingCountSliderValueSubject:Subject<number> = new Subject<number>();
    ratingCountSliderValueObserveable:Observable<number>;

    set ratingCountSliderValue(value: number) {
        this._ratingCountSliderValue = value;
        this.ratingCountSliderValueSubject.next(value);
    }

    get ratingCountSliderValue () {
        return this._ratingCountSliderValue;
    }

    public ratingCountObserveable():Observable<number> {
        return this.ratingCountSliderValueObserveable;
    }

    public openRatingCountDialog(initWith:number) {
        this.ratingCountSliderValue = initWith;
        this.ratingCountShow = true;
    }


    mdSliderInput_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;
    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;
    }

    /* YEAR */
    yearShow:boolean = false;
    _yearSliderValue:number;
    yearSliderValueSubject:Subject<number> = new Subject<number>();
    yearSliderValueObserveable:Observable<number>;

    set yearSliderValue(value: number) {
        this._yearSliderValue = value;
        this.yearSliderValueSubject.next(value);
    }

    get yearSliderValue () {
        return this._yearSliderValue;
    }

    public yearObserveable():Observable<number> {
        return this.yearSliderValueObserveable;
    }

    public openYearDialog(initWith:number) {
        this.yearSliderValue = initWith;
        this.yearShow = true;
    }

    mdSliderInput_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
    }
}

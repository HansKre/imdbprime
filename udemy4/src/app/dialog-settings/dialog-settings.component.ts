import { Component } from '@angular/core';
import { MdDialogRef, MdSliderChange } from "@angular/material";
import { Subject } from "rxjs/Subject";
import { Observable } from "rxjs/Observable";
import { animate, keyframes, state, style, transition, trigger } from "@angular/animations";

@Component({
  selector: 'dialog-settings-component',
  templateUrl: './dialog-settings.component.html',
  styleUrls: ['./dialog-settings.component.css'],
    animations: [
        /* SCALE IN_OUT */
        trigger('scalingTrigger', [
            state('normal1', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal2', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal1 <=> normal2', [
                animate(300, keyframes([
                    style({opacity: 1, transform: 'scale(1)', offset: 0}),
                    style({opacity: 0.5, transform: 'scale(1.6)', offset: 0.5}),
                    style({opacity: 1, transform: 'scale(1)', offset: 1}),
                ]))
            ])
        ]),
    ]
})
export class DialogSettingsComponent {

    scalingState:string = "normal1";

    animate() {
        this.scalingState = (this.scalingState === "normal1" ? "normal2" : "normal1");
    }

    public title: string;
    public message: string;

    maxRatingCount:number;
    minYear:number;
    maxYear:number;

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
        this.animate();
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

    public openRatingCountDialog(initWith:number, maxRatingCount:number) {
        this.ratingCountSliderValue = initWith;
        this.maxRatingCount = maxRatingCount;
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

    public openYearDialog(initWith:number, minYear:number, maxYear:number) {
        this.yearSliderValue = initWith;
        this.minYear = minYear;
        this.maxYear = maxYear;
        this.yearShow = true;
    }

    mdSliderInput_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
    }
}
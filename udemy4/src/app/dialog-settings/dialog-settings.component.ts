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
        /* SCALE IN_OUT SLOW */
        trigger('scalingSlowTrigger', [
            state('normal1', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal2', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal1 <=> normal2, * => normal1, * => normal2', [
                animate(300, keyframes([
                    style({opacity: 1, transform: 'scale(1)', offset: 0}),
                    style({opacity: 0.5, transform: 'scale(1.6)', offset: 0.5}),
                    style({opacity: 1, transform: 'scale(1)', offset: 1}),
                ]))
            ])
        ]),
        /* SCALE IN_OUT FAST */
        trigger('scalingFastTrigger', [
            state('normal3', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal4', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal3 <=> normal4, * => normal3, * => normal4', [
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

    animateSlowly() {
        /*
        This does not work with two different animations:
        this.scalingState = (this.scalingState === "normal1" ? "normal2" : "normal1");

        When clicking on the slider, a single (input) event is triggered,
        this changes the state from normal1 to normal3:
            mdSliderInput_RatingValue: normal1
            from: normal1 to: normal3

        Immediately after that, even before animation is started, the (change) event gets triggered,
        this changes the state from normal3 back to normal1:
            mdSliderChange_RatingValue: normal3
            from: normal3 to: normal2

        Therefore, no state transition and no animation is triggered!
         */
        if (this.scalingState === "normal1") {
            this.scalingState = "normal2";
        } else if (this.scalingState === "normal2") {
            this.scalingState = "normal1";
        } else if (this.scalingState === "normal3") {
            // we don't want to trigger a second (and different) animation
            // this.scalingState = "normal2";
        } else if (this.scalingState === "normal4") {
            // this.scalingState = "normal1";
        }
    }

    animateFast() {
        if (this.scalingState === "normal3") {
            this.scalingState = "normal4";
        } else if (this.scalingState === "normal4") {
            this.scalingState = "normal3";
        } else if (this.scalingState === "normal2") {
            this.scalingState = "normal4";
        } else if (this.scalingState === "normal1") {
            this.scalingState = "normal3";
        }
    }

    public title: string;
    public message: string;

    maxRatingCount:number;
    maxRatingValue:number;
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

    public openRatingValueDialog(initWith:number, maxRatingValue:number) {
        this.ratingValueSliderValue = initWith;
        this.maxRatingValue = maxRatingValue;
        this.ratingValueShow = true;
    }

    mdSliderInput_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
        this.animateFast();
    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
        this.animateSlowly();
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
        this.animateFast();
    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;
        this.animateSlowly();
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
        this.animateFast();
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
        this.animateSlowly();
    }
}

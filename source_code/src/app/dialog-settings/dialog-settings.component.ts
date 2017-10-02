import { Component } from '@angular/core';
import { MdDialogRef, MdSliderChange } from "@angular/material";
import { Subject } from "rxjs/Subject";
import { Observable } from "rxjs/Observable";
import {
    SCALE_IN_OUT_SLOW_FAST_FAST_ANIMATION,
    SCALE_IN_OUT_SLOW_FAST_SLOW_ANIMATION
} from "../animations/scale-in-out-slow-fast.animation";

@Component({
    selector: 'dialog-settings-component',
    templateUrl: './dialog-settings.component.html',
    styleUrls: ['./dialog-settings.component.css'],
    animations: [
        SCALE_IN_OUT_SLOW_FAST_SLOW_ANIMATION,
        SCALE_IN_OUT_SLOW_FAST_FAST_ANIMATION,
    ]
})
export class DialogSettingsComponent {

    scalingStateYear:string = "normal1";
    scalingStateRatingValue:string = "normal1";
    scalingStateRatingCount:string = "normal1";

    animateSlowly(comp:string) {
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

        let scalingState:string;

        switch (comp) {
            case 'year': scalingState = this.scalingStateYear; break;
            case 'ratingValue': scalingState = this.scalingStateRatingValue; break;
            case 'ratingCount': scalingState = this.scalingStateRatingCount; break;
            default: break;
        }

        if (scalingState === "normal1") {
            scalingState = "normal2";
        } else if (scalingState === "normal2") {
            scalingState = "normal1";
        } else if (scalingState === "normal3") {
            // we don't want to trigger a second (and different) animation
            // this.scalingState = "normal2";
        } else if (scalingState === "normal4") {
            // this.scalingState = "normal1";
        }

        switch (comp) {
            case 'year': return this.scalingStateYear = scalingState;
            case 'ratingValue': return this.scalingStateRatingValue = scalingState;
            case 'ratingCount': return this.scalingStateRatingCount = scalingState;
            default: return 0;
        }
    }

    animateFast(comp:string) {
        let scalingState:string;

        switch (comp) {
            case 'year': scalingState = this.scalingStateYear; break;
            case 'ratingValue': scalingState = this.scalingStateRatingValue; break;
            case 'ratingCount': scalingState = this.scalingStateRatingCount; break;
            default: break;
        }
        if (scalingState === "normal3") {
            scalingState = "normal4";
        } else if (scalingState === "normal4") {
            scalingState = "normal3";
        } else if (scalingState === "normal2") {
            scalingState = "normal4";
        } else if (scalingState === "normal1") {
            scalingState = "normal3";
        }

        switch (comp) {
            case 'year': return this.scalingStateYear = scalingState;
            case 'ratingValue': return this.scalingStateRatingValue = scalingState;
            case 'ratingCount': return this.scalingStateRatingCount = scalingState;
            default: return 0;
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
        this.animateFast('ratingValue');
    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {
        this.ratingValueSliderValue = changeEvent.value;
        this.animateSlowly('ratingValue');
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
        this.animateFast('ratingCount');
    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {
        this.ratingCountSliderValue = changeEvent.value;
        this.animateSlowly('ratingCount');
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
        this.animateFast('year');
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {
        this.yearSliderValue = changeEvent.value;
        this.animateSlowly('year');
    }

    public openAllDialog(initWithYear:number, minYear:number, maxYear:number,
                         initWithRatingCount:number, maxRatingCount:number,
                         initWithRatingValue:number, maxRatingValue:number) {
        this.yearSliderValue = initWithYear;
        this.minYear = minYear;
        this.maxYear = maxYear;

        this.ratingCountSliderValue = initWithRatingCount;
        this.maxRatingCount = maxRatingCount;

        this.ratingValueSliderValue = initWithRatingValue;
        this.maxRatingValue = maxRatingValue;

        this.yearShow = true;
        this.ratingCountShow = true;
        this.ratingValueShow = true;
    }

    allObserverables() {
        return {
            year:this.yearObserveable(),
            ratingValue:this.ratingValueObserveable(),
            ratingCount:this.ratingCountObserveable()
        };
    }
}
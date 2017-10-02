import { Component } from '@angular/core';
import { MdDialogRef, MdSliderChange } from "@angular/material";
import {
    SCALE_IN_OUT_SLOW_FAST_FAST_ANIMATION,
    SCALE_IN_OUT_SLOW_FAST_SLOW_ANIMATION
} from "../animations/scale-in-out-slow-fast.animation";
import {ValuesService} from "../services/values.service";

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

    ratingValueShow:boolean = false;
    yearShow:boolean = false;
    ratingCountShow:boolean = false;

    constructor(public dialogRef: MdDialogRef<DialogSettingsComponent>,
                private valuesService: ValuesService) { }

    public openRatingValueDialog():void {
        this.ratingValueShow = true;
    }

    public openYearDialog():void {
        this.yearShow = true;
    }

    public openRatingCountDialog():void {
        this.ratingCountShow = true;
    }

    public openAllDialog():void {
        this.yearShow = true;
        this.ratingCountShow = true;
        this.ratingValueShow = true;
    }

    onRatingValueChanged(changeEvent: MdSliderChange) {
        this.valuesService.minRatingValueFilter = changeEvent.value;
        this.animateSlowly('ratingValue');
    }

    onRatingValueInput(changeEvent: MdSliderChange) {
        this.valuesService.minRatingValueFilter = changeEvent.value;
        this.animateFast('ratingValue');
    }


    onRatingCountChanged(changeEvent: MdSliderChange) {
        this.valuesService.minRatingCountFilter = changeEvent.value;
        this.animateFast('ratingCount');
    }

    onRatingCountInput(changeEvent: MdSliderChange) {
        this.valuesService.minRatingCountFilter = changeEvent.value;
        this.animateSlowly('ratingCount');
    }

    onYearChanged(changeEvent: MdSliderChange) {
        this.valuesService.minYearValueFilter = changeEvent.value;
        this.animateSlowly('year');
    }

    onYearInput(changeEvent: MdSliderChange) {
        this.valuesService.minYearValueFilter = changeEvent.value;
        this.animateFast('year');
    }
}
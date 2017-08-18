import { Component } from '@angular/core';
import {MdDialogRef, MdSliderChange} from "@angular/material";

@Component({
  selector: 'dialog-rating-value',
  templateUrl: './dialog-rating-value.component.html',
  styleUrls: ['./dialog-rating-value.component.css']
})
export class DialogRatingValueComponent {

    public title: string;
    public message: string;

    ratingCountSliderValue:number = 10000;
    ratingValueSliderValue:number = 6;
    yearSliderValue:number = 1950;

    showOptions:boolean = true;

  constructor(public dialogRef: MdDialogRef<DialogRatingValueComponent>) { }


    mdSliderInput_RatingCount(changeEvent: MdSliderChange) {

    }

    mdSliderChange_RatingCount(changeEvent: MdSliderChange) {

    }

    mdSliderInput_RatingValue(changeEvent: MdSliderChange) {

    }

    mdSliderChange_RatingValue(changeEvent: MdSliderChange) {

    }

    mdSliderInput_Year(changeEvent: MdSliderChange) {
    }

    mdSliderChange_Year(changeEvent: MdSliderChange) {

    }
}

import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';

@Component({
  selector: 'app-slider',
  templateUrl: './slider.component.html',
  styleUrls: ['./slider.component.css']
})
export class SliderComponent implements OnInit {

    @Input() sliderMax: number = 0;
    @Output() onSliderValueChanged = new EventEmitter<number>();

    sliderVal: number = 0;

    sliderChanged(sliderValue: number) {
        this.onSliderValueChanged.emit(sliderValue);
    }

    constructor() {
    }

    ngOnInit() {
    }

}

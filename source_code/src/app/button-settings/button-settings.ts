import {EventEmitter, Input, Output} from "@angular/core";

export abstract class AbstractButtonSettingsComponent {

    @Input() maxRatingCount: number;
    @Input() maxRatingValue: number;
    @Input() maxYear: number;
    @Input() minYear: number;

    @Input() minRatingCountFilter:number;
    @Input() minRatingValueFilter:number;
    @Input() minYearValueFilter:number;

    @Output() onRatingCountChanged = new EventEmitter<number>();
    @Output() onRatingValueChanged = new EventEmitter<number>();
    @Output() onYearChanged = new EventEmitter<number>();

    constructor() { }

    _onRatingCountChanged(newValue:number) {
        if (this.minRatingCountFilter != newValue) {
            this.minRatingCountFilter = newValue;
            this.onRatingCountChanged.emit(newValue);
        }
    }

    _onRatingValueChanged(newValue:number) {
        if (this.minRatingValueFilter != newValue) {
            this.minRatingValueFilter = newValue;
            this.onRatingValueChanged.emit(newValue);
        }
    }

    _onYearChanged(newValue:number) {
        if (this.minYearValueFilter != newValue) {
            this.minYearValueFilter = newValue;
            this.onYearChanged.emit(newValue);
        }
    }
}

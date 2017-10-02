import {EventEmitter, Input, Output} from "@angular/core";

export abstract class AbstractButtonSettingsComponent {

    @Input() maxRatingCount: number = 0;
    @Input() maxRatingValue: number = 0;
    @Input() maxYear: number = 0;
    @Input() minYear: number = 0;

    @Input() minRatingCountFilter:number = 0;
    @Input() minRatingValueFilter:number = 0;
    @Input() minYearValueFilter:number = 0;

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

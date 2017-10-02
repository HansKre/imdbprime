import {EventEmitter, Output} from "@angular/core";
import {ValuesService} from "../services/values.service";

export abstract class AbstractButtonSettingsComponent {

    @Output() onRatingCountChanged = new EventEmitter<number>();
    @Output() onRatingValueChanged = new EventEmitter<number>();
    @Output() onYearChanged = new EventEmitter<number>();

    constructor(protected valuesService: ValuesService) { }

    _onRatingCountChanged(newValue:number) {
        if (this.valuesService.minRatingCountFilter != newValue) {
            this.valuesService.minRatingCountFilter = newValue;
            this.onRatingCountChanged.emit(newValue);
        }
    }

    _onRatingValueChanged(newValue:number) {
        if (this.valuesService.minRatingValueFilter != newValue) {
            this.valuesService.minRatingValueFilter = newValue;
            this.onRatingValueChanged.emit(newValue);
        }
    }

    _onYearChanged(newValue:number) {
        if (this.valuesService.minYearValueFilter != newValue) {
            this.valuesService.minYearValueFilter = newValue;
            //this.onYearChanged.emit(newValue);
        }
    }
}

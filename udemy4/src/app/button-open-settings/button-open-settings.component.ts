import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {DialogSettingsService} from "../dialog-settings/dialog-settings.service";

@Component({
    selector: 'app-button-open-settings',
    templateUrl: './button-open-settings.component.html',
    styleUrls: ['./button-open-settings.component.css'],
})
export class ButtonOpenSettingsComponent implements OnInit {

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

    constructor(public dialogSettingsService: DialogSettingsService) { }

    ngOnInit() {
    }

    openAllSettingsDialog() {
        let allObserverables = this.dialogSettingsService
            .openAllDialog(
                this.minYearValueFilter,
                this.minYear, this.maxYear,
                this.minRatingCountFilter,
                this.maxRatingCount,
                this.minRatingValueFilter,
                this.maxRatingValue);

        allObserverables.year.subscribe(
            newValue => this._onYearChanged(newValue));
        allObserverables.ratingValue.subscribe(
            newValue => this._onRatingValueChanged(newValue));
        allObserverables.ratingCount.subscribe(
            newValue => this._onRatingCountChanged(newValue));
    }

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

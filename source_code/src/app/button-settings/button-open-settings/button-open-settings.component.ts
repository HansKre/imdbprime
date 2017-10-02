import {Component, OnInit} from '@angular/core';
import {DialogSettingsService} from "../../dialog-settings/dialog-settings.service";
import {AbstractButtonSettingsComponent} from "../button-settings";

@Component({
    selector: 'app-button-open-settings',
    templateUrl: './button-open-settings.component.html',
    styleUrls: ['./button-open-settings.component.css'],
})
export class ButtonOpenSettingsComponent extends AbstractButtonSettingsComponent implements OnInit {

    constructor(public dialogSettingsService: DialogSettingsService) {
        super();
    }

    ngOnInit() { }

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
}

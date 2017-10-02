import {Component, OnInit} from '@angular/core';
import {DialogSettingsService} from "../../dialog-settings/dialog-settings.service";
import {AbstractButtonSettingsComponent} from "../button-settings";
import {ValuesService} from "../../services/values.service";

@Component({
    selector: 'app-button-open-settings',
    templateUrl: './button-open-settings.component.html',
    styleUrls: ['./button-open-settings.component.css'],
})
export class ButtonOpenSettingsComponent extends AbstractButtonSettingsComponent implements OnInit {

    constructor(public dialogSettingsService: DialogSettingsService,
                protected valuesService: ValuesService) {
        super(valuesService);
    }

    ngOnInit() { }

    openAllSettingsDialog() {
        let allObserverables = this.dialogSettingsService
            .openAllDialog(
                this.valuesService.minYearValueFilter,
                this.valuesService.minYear, this.valuesService.maxYear,
                this.valuesService.minRatingCountFilter,
                this.valuesService.maxRatingCount,
                this.valuesService.minRatingValueFilter,
                this.valuesService.maxRatingValue);

        allObserverables.year.subscribe(
            newValue => this._onYearChanged(newValue));
        allObserverables.ratingValue.subscribe(
            newValue => this._onRatingValueChanged(newValue));
        allObserverables.ratingCount.subscribe(
            newValue => this._onRatingCountChanged(newValue));
    }
}

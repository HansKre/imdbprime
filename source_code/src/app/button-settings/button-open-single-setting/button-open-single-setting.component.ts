import {Component, Input, OnInit} from '@angular/core';
import {SCALE_IN_OUT_SLOW_ANIMATION} from "../../animations/scale-in-out-slow.animation";
import {DialogSettingsService} from "../../dialog-settings/dialog-settings.service";
import {AbstractButtonSettingsComponent} from "../button-settings";

@Component({
    selector: 'app-button-open-single-setting',
    templateUrl: './button-open-single-setting.component.html',
    styleUrls: ['./button-open-single-setting.component.css'],
    animations: [
        SCALE_IN_OUT_SLOW_ANIMATION,
    ]
})
export class ButtonOpenSingleSettingComponent extends AbstractButtonSettingsComponent implements OnInit {

    @Input() settingYear:boolean = false;
    @Input() settingRatingValue:boolean = false;
    @Input() settingRatingCount:boolean = false;

    animationYearScalingState:string = "normal1";
    animationRatingValueScalingState:string = "normal1";
    animationRatingCountScalingState:string = "normal1";

    constructor(public dialogSettingsService: DialogSettingsService) {
        super();
    }

    ngOnInit() { }

    openRatingValueDialog() {
        this.animateRatingValueButton();
        this.dialogSettingsService
            .openRatingValueDialog(this.minRatingValueFilter, this.maxRatingValue)
            .subscribe(newValue => this._onRatingValueChanged(newValue));
    }

    openRatingCountDialog() {
        this.animateRatingCountButton();
        this.dialogSettingsService
            .openRatingCountDialog(this.minRatingCountFilter, this.maxRatingCount)
            .subscribe(newValue => this._onRatingCountChanged(newValue));
    }

    openYearDialog() {
        this.animateYearButton();
        this.dialogSettingsService
            .openYearDialog(this.minYearValueFilter, this.minYear, this.maxYear)
            .subscribe(newValue => this._onYearChanged(newValue));
    }

    animateYearButton() {
        this.animationYearScalingState =
            (this.animationYearScalingState
            === "normal1"
                ? this.animationYearScalingState = "normal2"
                : this.animationYearScalingState = "normal1");
    }

    animateRatingValueButton() {
        this.animationRatingValueScalingState =
            (this.animationRatingValueScalingState
            === "normal1"
                ? this.animationRatingValueScalingState = "normal2"
                : this.animationRatingValueScalingState = "normal1");
    }

    animateRatingCountButton() {
        this.animationRatingCountScalingState =
            (this.animationRatingCountScalingState
            === "normal1"
                ? this.animationRatingCountScalingState = "normal2"
                : this.animationRatingCountScalingState = "normal1");
    }
}

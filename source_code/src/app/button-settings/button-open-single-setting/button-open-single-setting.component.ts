import {Component, Input, OnInit} from '@angular/core';
import {SCALE_IN_OUT_SLOW_ANIMATION} from "../../animations/scale-in-out-slow.animation";
import {DialogSettingsService} from "../../dialog-settings/dialog-settings.service";
import {ValuesService} from "../../services/values.service";

@Component({
    selector: 'app-button-open-single-setting',
    templateUrl: './button-open-single-setting.component.html',
    styleUrls: ['./button-open-single-setting.component.css'],
    animations: [
        SCALE_IN_OUT_SLOW_ANIMATION,
    ]
})
export class ButtonOpenSingleSettingComponent implements OnInit {

    @Input() settingYear:boolean = false;
    @Input() settingRatingValue:boolean = false;
    @Input() settingRatingCount:boolean = false;

    animationYearScalingState:string = "normal1";
    animationRatingValueScalingState:string = "normal1";
    animationRatingCountScalingState:string = "normal1";

    constructor(public dialogSettingsService: DialogSettingsService,
                private valuesService: ValuesService) { }

    ngOnInit() { }

    openRatingValueDialog() {
        this.animateRatingValueButton();
        this.dialogSettingsService.openRatingValueDialog();
    }

    openRatingCountDialog() {
        this.animateRatingCountButton();
        this.dialogSettingsService.openRatingCountDialog();
    }

    openYearDialog() {
        this.animateYearButton();
        this.dialogSettingsService.openYearDialog();
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

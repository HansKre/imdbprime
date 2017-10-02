import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {SCALE_IN_OUT_SLOW_ANIMATION} from "../animations/scale-in-out-slow.animation";
import {DialogSettingsService} from "../dialog-settings/dialog-settings.service";

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

    animationYearScalingState:string = "normal1";
    animationRatingValueScalingState:string = "normal1";
    animationRatingCountScalingState:string = "normal1";

    constructor(public dialogSettingsService: DialogSettingsService) { }

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

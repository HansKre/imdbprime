import {Component, OnInit} from '@angular/core';
import {DialogSettingsService} from "../../dialog-settings/dialog-settings.service";

@Component({
    selector: 'app-button-open-settings',
    templateUrl: './button-open-settings.component.html',
    styleUrls: ['./button-open-settings.component.css'],
})
export class ButtonOpenSettingsComponent implements OnInit {

    constructor(public dialogSettingsService: DialogSettingsService) {
    }

    ngOnInit() { }

    openAllSettingsDialog() {
        this.dialogSettingsService.openAllDialog();
    }
}

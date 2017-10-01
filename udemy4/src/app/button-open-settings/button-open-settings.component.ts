import { Component, OnInit } from '@angular/core';

@Component({
    selector: 'app-button-open-settings',
    templateUrl: './button-open-settings.component.html',
    styleUrls: ['./button-open-settings.component.css']
})
export class ButtonOpenSettingsComponent implements OnInit {

    constructor() { }

    ngOnInit() {
    }

    openAllSettingsDialog() {
        alert("opening dialog");
    }

}

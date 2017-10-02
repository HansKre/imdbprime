import {Component, Input, OnInit} from '@angular/core';
import {ValuesService} from "../services/values.service";

@Component({
    selector: 'app-progress-bar',
    templateUrl: './progress-bar.component.html',
    styleUrls: ['./progress-bar.component.css']
})
export class ProgressBarComponent implements OnInit {

    @Input() isParentLoading:boolean = false;

    constructor(private valuesService: ValuesService) { }

    ngOnInit() { }

    shouldShowProgressBar() {
        if (navigator.onLine) {
            return this.valuesService.shouldLoadMoviesFromServer || this.isParentLoading;
        } else {
            return false;
        }
    }

}

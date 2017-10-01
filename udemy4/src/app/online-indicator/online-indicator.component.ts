import { Component, OnInit } from '@angular/core';
import { IsOnlineService } from "../services/is-online.service";
import {SCALE_IN_OUT_ANIMATION} from "../animations/scale-in-out.animation";

@Component({
    selector: 'app-online-indicator',
    templateUrl: './online-indicator.component.html',
    styleUrls: ['./online-indicator.component.css'],
    animations: [
        SCALE_IN_OUT_ANIMATION,
    ]
})
export class OnlineIndicatorComponent implements OnInit {

    isOnlineAnimationState:string = "normal1";

    constructor(public isOnlineService: IsOnlineService) { }

    ngOnInit() {
        this.isOnlineService.isOnlineObserveable()
            .subscribe((isOnline:boolean) => this.animate());
    }

    /* FOR DEBUGGING
    simulateOnlineStatusChange() {
        this.isOnlineService.isOnline = !this.isOnlineService.isOnline;
    }*/

    animate() {
        this.isOnlineAnimationState = (this.isOnlineAnimationState === "normal1" ? "normal2" : "normal1");
    }

}

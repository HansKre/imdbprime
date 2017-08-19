import { Component, OnInit } from '@angular/core';
import { IsOnlineService } from "../services/is-online.service";
import {animate, keyframes, state, style, transition, trigger} from "@angular/animations";

@Component({
    selector: 'app-online-indicator',
    templateUrl: './online-indicator.component.html',
    styleUrls: ['./online-indicator.component.css'],
    animations: [
        /* SCALE IN_OUT */
        trigger('isOnlineTrigger', [
            state('normal1', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal2', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal1 <=> normal2', [
                animate(800, keyframes([
                    style({opacity: 1, transform: 'scale(1)', offset: 0}),
                    style({opacity: 0.5, transform: 'scale(1.6)', offset: 0.5}),
                    style({opacity: 1, transform: 'scale(1)', offset: 1}),
                ]))
            ])
        ]),
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

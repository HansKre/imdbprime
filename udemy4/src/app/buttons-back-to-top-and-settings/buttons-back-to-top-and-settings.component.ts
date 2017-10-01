import {Component, HostListener, OnInit} from '@angular/core';
import {FADE_IN_OUT_ANIMATION} from "../animations/fade-in-out.animation";

@Component({
    selector: 'app-buttons-back-to-top-and-settings',
    templateUrl: './buttons-back-to-top-and-settings.component.html',
    styleUrls: ['./buttons-back-to-top-and-settings.component.css'],
    animations: [
        FADE_IN_OUT_ANIMATION,
    ]
})
export class MoveToTopComponent implements OnInit {

    /* SCROLL TO TOP */
    shouldShowScrollToTop: boolean = false;
    animateButtonEntryState: string = "in";

    constructor() { }

    ngOnInit() { }

    /* HOST LISTENER FOR STICKY TABLE HEAD AND SCROLL TO TOP */
    @HostListener('window:scroll', ['$event'])
    onWindowScroll(event) {
        this.onScrollFadeInOutScrollToTopButton();
    }

    onScrollFadeInOutScrollToTopButton() {
        /* The pageXOffset and pageYOffset properties returns the pixels the current document has been scrolled from the upper left corner of the window, horizontally and vertically.
        The pageXOffset and pageYOffset properties are equal to the scrollX and scrollY properties. These properties are read-only. */
        this.shouldShowScrollToTop = (window.pageYOffset >= window.screen.height/2);
    }

    /* Smooth scrolling */
    // https://stackoverflow.com/questions/36092212/smooth-scroll-angular2
    scrollTo(yPoint: number, duration: number) {
        setTimeout(() => {
            window.scrollTo(0, yPoint)
        }, duration);
        return;
    }

    smoothScrollToTop() {
        let startY = MoveToTopComponent.currentYPosition();
        let stopY = 0; // window top
        let distance = stopY > startY ? stopY - startY : startY - stopY;
        if (distance < 100) {
            window.scrollTo(0, stopY);
            return;
        }
        let speed = Math.round(distance / 50);
        let step = speed;

        const minSpeed = 9;
        speed = Math.max(minSpeed, speed); //min 9 otherwise it won't look smooth
        let leapY = stopY > startY ? startY + step : startY - step;
        let timer = 0;
        if (stopY > startY) {
            for (let i = startY; i < stopY; i += step) {
                // since setTimeout is asynchronous, the for-loop will will fire all scrolls
                // nearly simoultaniously. Therefore, we need to multiply the speed with
                // a counter which lets the scrolls start with a growing offset which lets the
                // setTimeout wait for a growing time till it scrolls there
                // that way, we prevent the window to scroll instantly to the target Yposition
                this.scrollTo(leapY, timer * speed);
                leapY += step; if (leapY > stopY) leapY = stopY; timer++;
            }
            return;
        } else {
            for (let i = startY; i > stopY; i -= step) {
                this.scrollTo(leapY, timer * speed);
                leapY -= step; if (leapY < stopY) leapY = stopY; timer++;
            }
        }
    }

    static currentYPosition() {
        // Firefox, Chrome, Opera, Safari
        if (self.pageYOffset) return self.pageYOffset;
        // Internet Explorer 6 - standards mode
        if (document.documentElement && document.documentElement.scrollTop)
            return document.documentElement.scrollTop;
        // Internet Explorer 6, 7 and 8
        if (document.body.scrollTop) return document.body.scrollTop;
        return 0;
    }



    //==============SETTINGS DIALOG=============
    openAllSettingsDialog() {
        alert("opening dialog");
    }

}

import { Component, Input, OnInit } from '@angular/core';
import {ValuesService} from "../services/values.service";

@Component({
    selector: 'app-movies-container',
    templateUrl: './movies.container.component.html',
    styleUrls: ['./movies.container.component.css'],
})

// it is possible to animate the transition between 2 states, just use animate()
// for animating from void, specify the starting state for animation
// it is also possible to specify the end state when animation finishes
// ... after that, animation transitions immediately to the target state?

export class MoviesContainerComponent implements OnInit {
    ngOnInit(): void { }

    @Input() isParentLoading:boolean = false;

    constructor(private valuesService: ValuesService) {
    }
}
import {animate, keyframes, state, style, transition, trigger} from '@angular/animations';

export const SCALE_IN_OUT_ANIMATION =
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
    ]);
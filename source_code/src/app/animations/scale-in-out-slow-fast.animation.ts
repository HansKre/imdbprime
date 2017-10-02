import {animate, keyframes, state, style, transition, trigger} from '@angular/animations';

export const SCALE_IN_OUT_SLOW_FAST_SLOW_ANIMATION =
        /* SCALE IN_OUT SLOW */
        trigger('scalingSlowTrigger', [
            state('normal1', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            state('normal2', style({
                opacity: 1,
                transform: 'scale(1)'
            })),
            transition('normal1 <=> normal2, * => normal1, * => normal2', [
                animate(300, keyframes([
                    style({opacity: 1, transform: 'scale(1)', offset: 0}),
                    style({opacity: 0.5, transform: 'scale(1.6)', offset: 0.5}),
                    style({opacity: 1, transform: 'scale(1)', offset: 1}),
                ]))
            ])
        ]);

export const SCALE_IN_OUT_SLOW_FAST_FAST_ANIMATION =
    trigger('scalingFastTrigger', [
    state('normal3', style({
        opacity: 1,
        transform: 'scale(1)'
    })),
    state('normal4', style({
        opacity: 1,
        transform: 'scale(1)'
    })),
    transition('normal3 <=> normal4, * => normal3, * => normal4', [
        animate(300, keyframes([
            style({opacity: 1, transform: 'scale(1)', offset: 0}),
            style({opacity: 0.5, transform: 'scale(1.6)', offset: 0.5}),
            style({opacity: 1, transform: 'scale(1)', offset: 1}),
        ]))
    ])
]);
import { animate, state, style, transition, trigger } from '@angular/animations';

export const FADE_IN_OUT_ANIMATION =
    trigger('fadeInOutTrigger', [
        state('in', style({
            opacity: 1,
            transform: 'translateY(0)'
        })),
        transition('void => in', [
            // specify starting style for animation
            style({
                opacity: 0,
                transform: 'translateY(-100%)'
            }),
            animate(300)
        ]),
        transition('in => void', [
            // specify end state after animation
            animate(300,
                style({
                    opacity: 0,
                    transform: 'translateY(-100%)'
                })
            )
        ])
    ]);
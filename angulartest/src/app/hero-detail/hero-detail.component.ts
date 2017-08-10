import {Component, Input, OnInit} from '@angular/core';
import { Hero } from '../hero/hero';
import { HeroService } from "../hero-service/hero.service";
import {ActivatedRoute, ParamMap} from "@angular/router";
import { Location } from "@angular/common";
import 'rxjs/add/operator/switchMap';

@Component({
    //CSS-Selector
    //will match the element tag that identifies this component within a parent component's template
    selector: 'hero-detail',
    templateUrl: './hero-detail.component.html',
    styleUrls: ['./hero-detail.component.css']
})

export class HeroDetailComponent implements OnInit{

    @Input() hero: Hero;

    constructor(
        private heroService: HeroService,
        private route: ActivatedRoute,
        private location: Location
    ) {}

    ngOnInit(): void {
        this.route.paramMap
            .switchMap((params: ParamMap) =>
                this.heroService.getHeroSlowly(+params.get('id')))
            .subscribe(hero => this.hero = hero);
    }

    goBack(): void {
        this.location.back();
    }

}

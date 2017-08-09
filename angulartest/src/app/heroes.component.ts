import {Component, OnInit} from '@angular/core';
import { Hero } from "./hero";
import { HeroService } from "./hero.service";
import {Router} from "@angular/router";

@Component({
    selector: 'my-heroes',
    templateUrl: './heroes.component.html',
    styleUrls: ['./heroes.component.css'],
})
export class HeroesComponent implements OnInit {
    title = 'Tour of Heroes';
    heroes: Hero[];
    selectedHero: Hero;

    // The parameter simultaneously defines a private heroService property
    // and identifies it as a HeroService injection site.
    // Now Angular knows to supply an instance of the HeroService when
    // it creates an AppComponent
    constructor(
        private heroService: HeroService,
        private router: Router
    ) {}

    onSelect(hero: Hero): void {
        if (this.selectedHero == hero) {
            this.selectedHero = null;
        } else {
            this.selectedHero = hero;
        }
    }

    getHeroes(): void {
        this.heroService.getHeroesSlowly().then(heroes => this.heroes = heroes);
    }

    ngOnInit(): void {
        this.getHeroes();
    }

    gotoDetail(): void {
        this.router.navigate(['detail', this.selectedHero.id]);
    }
}

import { Injectable } from '@angular/core';

import { Hero } from "../hero/hero";
import { HEROES } from "./mock-heroes";
import { Http } from "@angular/http";
import "rxjs/add/operator/toPromise";

@Injectable()
export class HeroService {
    // URL to web api
    private heroesUrl = 'api/heroes';

    getHeroesOld(): Promise<Hero[]> {
    return Promise.resolve(HEROES);
    }

    getHeroesSlowly(): Promise<Hero[]> {
        return new Promise(resolve => {
            // Simulate server latency with 2 second delay
            setTimeout(() => resolve(this.getHeroes()), 500);
        });
    }

    getHeroSlowly(id: number): Promise<Hero> {
        return this.getHeroesSlowly()
            .then(heroes => heroes.find(hero => hero.id === id));
    }

    constructor(private http: Http) {}

    getHeroes(): Promise<Hero[]> {
        return this.http.get(this.heroesUrl)
            .toPromise()
            .then(response => response.json().data as Hero[])
            .catch(this.handleError);
    }

    private handleError(error: any): Promise<any> {
        console.error('An error occurred', error); // for demo purposes only
        return Promise.reject(error.message || error);
    }
}

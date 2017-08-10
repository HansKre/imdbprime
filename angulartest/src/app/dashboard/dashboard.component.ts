import { Component, OnInit } from '@angular/core';

import { HeroService } from '../hero-service/hero.service';
import {Hero} from "../hero/hero";

@Component({
  selector: 'my-dashboard',
  templateUrl: '../dashboard/dashboard.component.html',
  styleUrls: [ '../dashboard/dashboard.component.css' ]
})
export class DashboardComponent implements OnInit {

  heroes: Hero[] = [];

  constructor(private heroService: HeroService) { }

  ngOnInit(): void {
    this.heroService.getHeroes()
      .then(heroes => this.heroes = heroes.slice(1, 5));
  }
}

import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";

import { AppComponent } from './app.component';
import { HeroDetailComponent } from "./hero-detail/hero-detail.component";
import { HeroesComponent } from "./heroes/heroes.component";
import { HeroService } from "./hero-service/hero.service";
import { DashboardComponent } from "./dashboard/dashboard.component";
import { AppRoutingModule } from "./app-routing/app-routing.module";
import { HttpModule } from "@angular/http";

import { InMemoryWebApiModule } from 'angular-in-memory-web-api';
import { InMemoryDataService } from './in-memory-data.service';

@NgModule({
    /*
    the declarations array contains a list of application components,
    pipes, and directives that belong to the module. A component must
    be declared in a module before other components can reference it.*/
  declarations: [
      AppComponent,
      HeroDetailComponent,
      HeroesComponent,
      DashboardComponent
  ],
  imports: [
      BrowserModule,
      FormsModule,
      AppRoutingModule,
      HttpModule,
      InMemoryWebApiModule.forRoot(InMemoryDataService)
  ],
  providers: [HeroService],
  bootstrap: [AppComponent]
})

export class AppModule { }

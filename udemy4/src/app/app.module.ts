import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";
import { HttpModule } from "@angular/http";

import { AppComponent } from './app.component';
import { VideosComponent } from './videos/videos.component';
import { SliderComponent } from './slider/slider.component';

import { WebService } from "./services/web.service";

@NgModule({
  declarations: [
      AppComponent,
      VideosComponent,
      SliderComponent
  ],
  imports: [
      BrowserModule,
      FormsModule,
      HttpModule
  ],
  providers: [WebService],
  bootstrap: [AppComponent]
})
export class AppModule { }

import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";
import { HttpModule } from "@angular/http";

//Angular Material Modules
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {
    MdProgressBarModule, MdSliderModule, MdSlideToggle, MdSlideToggleModule,
    MdSnackBarModule
} from '@angular/material';

// My Components
import { AppComponent } from './app.component';
import { VideosComponent } from './videos/videos.component';
import { SliderComponent } from './slider/slider.component';
import { ResultsComponent } from './results/results.component';

import { WebService } from "./services/web.service";

// needed for some gesture support
import 'hammerjs';

@NgModule({
  declarations: [
      AppComponent,
      VideosComponent,
      SliderComponent,
      ResultsComponent
  ],
  imports: [
      BrowserModule,
      FormsModule,
      HttpModule,
      MdSliderModule,
      MdProgressBarModule,
      MdSnackBarModule,
      BrowserAnimationsModule,
      MdSlideToggleModule
  ],
  providers: [WebService],
  bootstrap: [AppComponent]
})
export class AppModule { }

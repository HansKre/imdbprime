import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";
import { HttpModule } from "@angular/http";

//Angular Material Modules
import { BrowserAnimationsModule } from "@angular/platform-browser/animations";
import {
    MdButtonModule, MdDialogModule, MdInputModule /* for Md-Container-Input */,
    MdProgressBarModule, MdSliderModule,
    MdSnackBarModule, MdSortModule
} from '@angular/material';

// My Components
import { AppComponent } from './app.component';
import { MoviesContainerComponent } from './movies-container/movies.container.component';
import { SliderComponent } from './slider/slider.component';
import { ResultsComponent } from './results/results.component';
import { OnlineIndicatorComponent } from './online-indicator/online-indicator.component';
import { DialogSettingsComponent } from './dialog-settings/dialog-settings.component';
import { MoveToTopComponent } from './buttons-back-to-top-and-settings/buttons-back-to-top-and-settings.component';

// Services
import { WebService } from "./services/web.service";
import { IsOnlineService } from "./services/is-online.service";
import { DialogSettingsService } from "./dialog-settings/dialog-settings.service";

// needed for some gesture support
import 'hammerjs';
// needed to support animations in Safari and Firefox
// https://github.com/angular/angular/issues/10420
import 'web-animations-js';

@NgModule({
    declarations: [
        AppComponent,
        MoviesContainerComponent,
        SliderComponent,
        ResultsComponent,
        OnlineIndicatorComponent,
        DialogSettingsComponent,
        MoveToTopComponent
    ],
    imports: [
        BrowserModule,
        FormsModule,
        HttpModule,
        MdSliderModule,
        MdProgressBarModule,
        MdSnackBarModule,
        BrowserAnimationsModule,
        MdButtonModule,
        MdDialogModule,
        MdInputModule,
        MdSortModule
    ],
    providers: [
        WebService,
        IsOnlineService,
        DialogSettingsService
    ],
    entryComponents: [
        DialogSettingsComponent
    ],
    bootstrap: [AppComponent]
})
export class AppModule { }

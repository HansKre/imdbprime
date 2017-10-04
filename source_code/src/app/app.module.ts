import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from "@angular/forms";
import { HttpModule } from "@angular/http";

//Angular Material Modules
import { BrowserAnimationsModule } from "@angular/platform-browser/animations";
import {
    MdButtonModule, MdDialogModule, MdInputModule /* for Md-Container-Input */,
    MdProgressBarModule, MdSliderModule,
    MdSnackBarModule, MdSortModule, MatSortModule, MatTableModule, MdIconModule
} from '@angular/material';

// My Components
import { AppComponent } from './app.component';
import { MoviesContainerComponent } from './movies-container/movies.container.component';
import { SliderComponent } from './slider/slider.component';
import { ResultsComponent } from './results/results.component';
import { OnlineIndicatorComponent } from './online-indicator/online-indicator.component';
import { DialogSettingsComponent } from './dialog-settings/dialog-settings.component';
import { MoveToTopComponent } from './button-back-to-top/button-back-to-top.component';
import { ButtonOpenSettingsComponent } from './button-settings/button-open-settings/button-open-settings.component';
import { ButtonOpenSingleSettingComponent } from './button-settings/button-open-single-setting/button-open-single-setting.component';
import { MoviesTableComponent } from './movies-table/movies-table.component';
import { ProgressBarComponent } from './progress-bar/progress-bar.component';
import { ShowingInfoComponent } from './showing-info/showing-info.component';
import { MoviesTableMatComponent } from './movies-table-mat/movies-table-mat.component';

// Services
import { WebService } from "./services/web.service";
import { IsOnlineService } from "./services/is-online.service";
import { DialogSettingsService } from "./dialog-settings/dialog-settings.service";
import { ValuesService } from "./services/values.service";

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
        MoveToTopComponent,
        ButtonOpenSettingsComponent,
        ButtonOpenSingleSettingComponent,
        MoviesTableComponent,
        ProgressBarComponent,
        ShowingInfoComponent,
        MoviesTableMatComponent
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
        MdSortModule,
        MatTableModule,
        MatSortModule,
        MdIconModule
    ],
    providers: [
        WebService,
        IsOnlineService,
        DialogSettingsService,
        ValuesService,
    ],
    entryComponents: [
        DialogSettingsComponent
    ],
    bootstrap: [AppComponent]
})
export class AppModule { }

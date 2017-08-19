import { Injectable } from '@angular/core';
import {MdDialog, MdDialogRef} from "@angular/material";
import {Observable} from "rxjs/Observable";
import {DialogSettingsComponent} from "./dialog-settings.component";

@Injectable()
export class DialogSettingsService {

  constructor(private dialog: MdDialog) { }

  public confirm(title:string, message:string): Observable<boolean> {
      let dialogRef: MdDialogRef<DialogSettingsComponent>;

      dialogRef = this.dialog.open(DialogSettingsComponent);
      dialogRef.componentInstance.title = title;
      dialogRef.componentInstance.message = message;

      return dialogRef.afterClosed();
  }

  public openRatingValueDialog(initWith:number): Observable<number> {
      let dialogRef: MdDialogRef<DialogSettingsComponent>;

      dialogRef = this.dialog.open(DialogSettingsComponent);
      dialogRef.componentInstance.openRatingValueDialog(initWith);

      return dialogRef.componentInstance.ratingValueObserveable();
  }

    public openRatingCountDialog(initWith:number, maxRatingCount:number): Observable<number> {
        let dialogRef: MdDialogRef<DialogSettingsComponent>;

        dialogRef = this.dialog.open(DialogSettingsComponent);
        dialogRef.componentInstance.openRatingCountDialog(initWith, maxRatingCount);

        return dialogRef.componentInstance.ratingCountObserveable();
    }

    public openYearDialog(initWith:number, minYear:number, maxYear:number): Observable<number> {
        let dialogRef: MdDialogRef<DialogSettingsComponent>;

        dialogRef = this.dialog.open(DialogSettingsComponent);
        dialogRef.componentInstance.openYearDialog(initWith, minYear, maxYear);

        return dialogRef.componentInstance.yearObserveable();
    }
}

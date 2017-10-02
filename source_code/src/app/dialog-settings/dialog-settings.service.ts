import { Injectable } from '@angular/core';
import {MdDialog, MdDialogRef} from "@angular/material";
import {Observable} from "rxjs/Observable";
import {DialogSettingsComponent} from "./dialog-settings.component";

@Injectable()
export class DialogSettingsService {

  constructor(private dialog: MdDialog) { }

  // only used as an example
  public confirm(title:string, message:string): Observable<boolean> {
      let dialogRef: MdDialogRef<DialogSettingsComponent>;

      dialogRef = this.dialog.open(DialogSettingsComponent);
      dialogRef.componentInstance.title = title;
      dialogRef.componentInstance.message = message;

      // Observer is used to listen for changes in the UI.
      // Use a Model or Service component to listen for Model-Changes!
      return dialogRef.afterClosed();
  }

  public openRatingValueDialog(): void {
      let dialogRef: MdDialogRef<DialogSettingsComponent>;

      dialogRef = this.dialog.open(DialogSettingsComponent);
      dialogRef.componentInstance.openRatingValueDialog();
  }

    public openRatingCountDialog(): void {
        let dialogRef: MdDialogRef<DialogSettingsComponent>;

        dialogRef = this.dialog.open(DialogSettingsComponent);
        dialogRef.componentInstance.openRatingCountDialog();
    }

    public openYearDialog(): void {
        let dialogRef: MdDialogRef<DialogSettingsComponent>;

        dialogRef = this.dialog.open(DialogSettingsComponent);
        dialogRef.componentInstance.openYearDialog();
    }

    public openAllDialog(): void {
        let dialogRef: MdDialogRef<DialogSettingsComponent>;

        dialogRef = this.dialog.open(DialogSettingsComponent);
        dialogRef.componentInstance.openAllDialog();
    }
}

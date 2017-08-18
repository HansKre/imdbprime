import { Injectable } from '@angular/core';
import {MdDialog, MdDialogRef} from "@angular/material";
import {Observable} from "rxjs/Observable";
import {DialogRatingValueComponent} from "./dialog-rating-value.component";

@Injectable()
export class DialogRatingValueService {

  constructor(private dialog: MdDialog) { }

  public confirm(title:string, message:string): Observable<boolean> {
      let dialogRef: MdDialogRef<DialogRatingValueComponent>;

      dialogRef = this.dialog.open(DialogRatingValueComponent);
      dialogRef.componentInstance.title = title;
      dialogRef.componentInstance.message = message;

      return dialogRef.afterClosed();
  }
}

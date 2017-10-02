import { Component, OnInit } from '@angular/core';
import {ValuesService} from "../services/values.service";

@Component({
  selector: 'app-showing-info',
  templateUrl: './showing-info.component.html',
  styleUrls: ['./showing-info.component.css']
})
export class ShowingInfoComponent implements OnInit {

  constructor(public valuesService: ValuesService) { }

  ngOnInit() { }

  showing():string {
      if (this.valuesService.displayedMovies) {
          return this.valuesService.displayedMovies.length.toString();
      } else {
          return "0";
      }
  }

  matching():string {
      if (this.valuesService.filteredMovies) {
          return this.valuesService.filteredMovies.length.toString();
      } else {
          return "0";
      }
  }

  total():string {
      if (this.valuesService.allMovies) {
          return this.valuesService.allMovies.length.toString();
      } else {
          return "0";
      }
  }
}

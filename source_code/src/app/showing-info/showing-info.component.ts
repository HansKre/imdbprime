import { Component, OnInit } from '@angular/core';
import {ValuesService} from "../services/values.service";

@Component({
  selector: 'app-showing-info',
  templateUrl: './showing-info.component.html',
  styleUrls: ['./showing-info.component.css']
})
export class ShowingInfoComponent implements OnInit {

  constructor(public valuesService: ValuesService) { }

  ngOnInit() {
  }

}

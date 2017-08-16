import { Component, OnInit } from '@angular/core';
import { IsOnlineService } from "../services/is-online.service";

@Component({
  selector: 'app-online-indicator',
  templateUrl: './online-indicator.component.html',
  styleUrls: ['./online-indicator.component.css']
})
export class OnlineIndicatorComponent implements OnInit {

  constructor(public globalService: IsOnlineService) { }

  ngOnInit() {
  }

}

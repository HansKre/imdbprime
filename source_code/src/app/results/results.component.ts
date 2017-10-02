import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-results',
  templateUrl: './results.component.html',
  styleUrls: ['./results.component.css']
})
export class ResultsComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}

/*
                        url: './getResultsDateAndCount.php',
                        success: function(data) {
                            resultsFromElem.innerHTML += data + '<br>' + "With min Rating Count: " + minRatingCount + ": "+ resultsArray.length;
                        }
                    });
 */

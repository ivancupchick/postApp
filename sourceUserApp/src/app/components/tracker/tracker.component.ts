import { Component, OnInit } from '@angular/core';
import { LetterHistory, ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-tracker',
  templateUrl: './tracker.component.html',
  styleUrls: ['./tracker.component.sass']
})
export class TrackerComponent implements OnInit {
  historyItems: LetterHistory[];

  track: string;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
  }

  get() {
    this.apiService.readLetterHistory(this.track)
      .subscribe(res => {
        this.historyItems = res[0].history
          .split('|')
          .filter((c, i) => i !== 0)
          .map(c => JSON.parse(c));
      });
  }
}

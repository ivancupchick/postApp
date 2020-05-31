import { Component, OnInit } from '@angular/core';
import { Letter, ApiService } from 'src/app/api.service';

@Component({
  selector: 'app-show-all-letters',
  templateUrl: './show-all-letters.component.html',
  styleUrls: ['./show-all-letters.component.sass']
})
export class ShowAllLettersComponent implements OnInit  {
  letters: Letter[];

  constructor(private apiService: ApiService) { }

  ngOnInit(): void {
    this.apiService.readLetters().subscribe((letters: Letter[]) => {
      this.letters = letters;
      console.log(this.letters);
    });
  }




}

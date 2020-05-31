import { Component, OnInit, ViewChild } from '@angular/core';
import { LetterFormComponent } from '../letter-form/letter-form.component';

@Component({
  selector: 'app-create-new-letter',
  templateUrl: './create-new-letter.component.html',
  styleUrls: ['./create-new-letter.component.sass']
})
export class CreateNewLetterComponent implements OnInit {

  @ViewChild(LetterFormComponent, { static: false }) form: LetterFormComponent;

  constructor() { }

  ngOnInit(): void {
  }

}

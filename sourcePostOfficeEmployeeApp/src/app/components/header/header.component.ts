import { Component, OnInit } from '@angular/core';
import * as jsPDf from 'jspdf';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.sass']
})
export class HeaderComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

  downloadQR() {
    const doc = new jsPDf();

    doc.text('rrr', 15, 15);
    doc.save('first.pdf');
  }
}

import { Component, OnInit } from '@angular/core';
import { LetterStatus } from 'src/app/services/dbletters.service';
import { StorageService } from 'src/app/services/storage.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.sass']
})
export class HomeComponent implements OnInit {
  letterStatus = LetterStatus;

  selectedItem;

  list = [{
    name: 'Принять в обработку',
    value: LetterStatus.onStartPostOffice
  }, {
    name: 'Отправить в другой филиал',
    value: LetterStatus.inProgress
  }, {
    name: 'Прием в промежуточном филиале',
    value: LetterStatus.onPromejutochniyPostOffice
  }, {
    name: 'Готов к выдаче',
    value: LetterStatus.onEndPostOffice
  }, {
    name: 'Вручить',
    value: LetterStatus.withoutStatus
  }];

  constructor(private storageService: StorageService) { }

  ngOnInit() {
  }

  changeStatus(status: LetterStatus) {
    this.storageService.setStatus(this.selectedItem);
  }

}

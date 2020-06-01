import { Injectable } from '@angular/core';
import { BehaviorSubject } from 'rxjs';
import { LetterStatus } from './dbletters.service';

@Injectable()
export class StorageService {
  status: BehaviorSubject<LetterStatus> = new BehaviorSubject(LetterStatus.withoutStatus);

  constructor() { }

  setStatus(status: LetterStatus) {
    this.status.next(status);
  }
}

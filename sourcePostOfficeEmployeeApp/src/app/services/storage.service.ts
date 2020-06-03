import { Injectable } from '@angular/core';
import { BehaviorSubject, from, Observable, of } from 'rxjs';
import { LetterStatus } from './dbletters.service';
import { NgxIndexedDBService } from 'ngx-indexed-db';
import { tap } from 'rxjs/operators';

@Injectable()
export class StorageService {
  keyStore = 'ids';

  scannedIds = [];
  status: BehaviorSubject<LetterStatus> = new BehaviorSubject(LetterStatus.withoutStatus);

  constructor(private dbService: NgxIndexedDBService) { }

  setStatus(status: LetterStatus) {
    this.status.next(status);
  }

  clear() {
    this.dbService.clear(this.keyStore);
  }

  setId(id: number) {
    if (this.scannedIds.find(ider => ider === id)) {
      return of(0);
    }
    return from(this.dbService.add(this.keyStore, { ider: id }));
  }

  getAll(): Observable<any> {
    return from(this.dbService.getAll(this.keyStore))
      .pipe(
        tap(r => {
          this.scannedIds = r.map(rr => rr.ider);
        })
      );
  }
}

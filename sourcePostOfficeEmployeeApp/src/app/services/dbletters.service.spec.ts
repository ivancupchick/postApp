import { TestBed } from '@angular/core/testing';

import { DBLettersService } from './dbletters.service';

describe('DBLettersService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: DBLettersService = TestBed.get(DBLettersService);
    expect(service).toBeTruthy();
  });
});

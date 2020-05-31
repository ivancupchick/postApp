import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowAllLettersComponent } from './show-all-letters.component';

describe('ShowAllLettersComponent', () => {
  let component: ShowAllLettersComponent;
  let fixture: ComponentFixture<ShowAllLettersComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ShowAllLettersComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowAllLettersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

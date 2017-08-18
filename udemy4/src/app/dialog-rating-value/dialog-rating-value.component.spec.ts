import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DialogRatingValueComponent } from './dialog-rating-value.component';

describe('DialogRatingValueComponent', () => {
  let component: DialogRatingValueComponent;
  let fixture: ComponentFixture<DialogRatingValueComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DialogRatingValueComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DialogRatingValueComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

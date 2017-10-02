import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ShowingInfoComponent } from './showing-info.component';

describe('ShowingInfoComponent', () => {
  let component: ShowingInfoComponent;
  let fixture: ComponentFixture<ShowingInfoComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ShowingInfoComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ShowingInfoComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

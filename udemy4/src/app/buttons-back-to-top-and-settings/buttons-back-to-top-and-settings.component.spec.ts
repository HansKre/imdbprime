import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MoveToTopComponent } from './buttons-back-to-top-and-settings.component';

describe('MoveToTopComponent', () => {
  let component: MoveToTopComponent;
  let fixture: ComponentFixture<MoveToTopComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MoveToTopComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MoveToTopComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

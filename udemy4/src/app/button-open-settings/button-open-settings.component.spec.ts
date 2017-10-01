import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ButtonOpenSettingsComponent } from './button-open-settings.component';

describe('ButtonOpenSettingsComponent', () => {
  let component: ButtonOpenSettingsComponent;
  let fixture: ComponentFixture<ButtonOpenSettingsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ButtonOpenSettingsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ButtonOpenSettingsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

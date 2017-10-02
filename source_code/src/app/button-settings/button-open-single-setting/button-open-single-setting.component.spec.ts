import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ButtonOpenSingleSettingComponent } from './button-open-single-setting.component';

describe('ButtonOpenSingleSettingComponent', () => {
  let component: ButtonOpenSingleSettingComponent;
  let fixture: ComponentFixture<ButtonOpenSingleSettingComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ButtonOpenSingleSettingComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ButtonOpenSingleSettingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

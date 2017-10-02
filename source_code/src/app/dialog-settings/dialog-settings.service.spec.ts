import { TestBed, inject } from '@angular/core/testing';

import { DialogSettingsService } from './dialog-settings.service';

describe('DialogSettingsService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [DialogSettingsService]
    });
  });

  it('should be created', inject([DialogSettingsService], (service: DialogSettingsService) => {
    expect(service).toBeTruthy();
  }));
});

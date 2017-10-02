import { TestBed, inject } from '@angular/core/testing';

import { IsOnlineService } from './is-online.service';

describe('IsOnlineService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [IsOnlineService]
    });
  });

  it('should be created', inject([IsOnlineService], (service: IsOnlineService) => {
    expect(service).toBeTruthy();
  }));
});

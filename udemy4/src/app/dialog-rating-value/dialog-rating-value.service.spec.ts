import { TestBed, inject } from '@angular/core/testing';

import { DialogRatingValueService } from './dialog-rating-value.service';

describe('DialogRatingValueService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [DialogRatingValueService]
    });
  });

  it('should be created', inject([DialogRatingValueService], (service: DialogRatingValueService) => {
    expect(service).toBeTruthy();
  }));
});

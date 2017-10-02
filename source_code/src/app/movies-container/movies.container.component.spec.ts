import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MoviesContainerComponent } from './movies.container.component';

describe('VideosComponent', () => {
  let component: MoviesContainerComponent;
  let fixture: ComponentFixture<MoviesContainerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MoviesContainerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MoviesContainerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});

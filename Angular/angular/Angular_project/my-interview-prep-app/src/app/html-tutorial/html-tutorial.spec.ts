import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HtmlTutorial } from './html-tutorial';

describe('HtmlTutorial', () => {
  let component: HtmlTutorial;
  let fixture: ComponentFixture<HtmlTutorial>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HtmlTutorial]
    })
    .compileComponents();

    fixture = TestBed.createComponent(HtmlTutorial);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

import { ComponentFixture, TestBed } from '@angular/core/testing';

import { JavascriptTutorial } from './javascript-tutorial';

describe('JavascriptTutorial', () => {
  let component: JavascriptTutorial;
  let fixture: ComponentFixture<JavascriptTutorial>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [JavascriptTutorial]
    })
    .compileComponents();

    fixture = TestBed.createComponent(JavascriptTutorial);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

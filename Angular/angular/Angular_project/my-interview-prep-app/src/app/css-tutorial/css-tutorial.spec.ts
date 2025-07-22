import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CssTutorial } from './css-tutorial';

describe('CssTutorial', () => {
  let component: CssTutorial;
  let fixture: ComponentFixture<CssTutorial>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CssTutorial]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CssTutorial);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});

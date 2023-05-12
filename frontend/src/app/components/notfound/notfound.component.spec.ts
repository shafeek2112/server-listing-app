import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { NotfoundComponent } from './notfound.component';

describe('NotfoundComponent', () => {
  let component: NotfoundComponent;
  let fixture: ComponentFixture<NotfoundComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [NotfoundComponent],
      imports: [RouterTestingModule.withRoutes([
        { path: '404', component: NotfoundComponent },
        { path: '**', component: NotfoundComponent }
      ])]
    }).compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(NotfoundComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create the component', () => {
    expect(component).toBeTruthy();
  });

  it('should have a title "This page cannot be found"', () => {
    const title = fixture.nativeElement.querySelector('h1');
    expect(title.textContent).toContain('This page cannot be found');
  });
});

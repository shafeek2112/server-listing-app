import { ClickOutsideDirective } from './click-outside.directive';
import { Component, DebugElement } from '@angular/core';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { By } from '@angular/platform-browser';

@Component({
  template: `
    <div class="container" clickOutside (clickOutside)="onClickOutside($event)">
      <div class="inside"></div>
    </div>
  `
})
class TestComponent {
  onClickOutside(event: MouseEvent) {}
}

describe('ClickOutsideDirective', () => {
  let component: TestComponent;
  let fixture: ComponentFixture<TestComponent>;
  let container: DebugElement;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ClickOutsideDirective, TestComponent]
    }).compileComponents();

    fixture = TestBed.createComponent(TestComponent);
    component = fixture.componentInstance;
    container = fixture.debugElement.query(By.css('.container'));

    fixture.detectChanges();
  });

  it('should emit clickOutside event when clicking outside the container', () => {
    spyOn(component, 'onClickOutside');
    const outsideElement = document.createElement('div');
    document.body.appendChild(outsideElement);
    outsideElement.click();
    expect(component.onClickOutside).toHaveBeenCalled();
  });

  it('should not emit clickOutside event when clicking inside the container', () => {
    spyOn(component, 'onClickOutside');
    const insideElement = container.query(By.css('.inside'));
    insideElement.nativeElement.click();
    expect(component.onClickOutside).not.toHaveBeenCalled();
  });

  it('should not emit clickOutside event when clicking on the filter-dropdown element', () => {
    spyOn(component, 'onClickOutside');
    const dropdownElement = document.createElement('div');
    dropdownElement.classList.add('filter-dropdown');
    document.body.appendChild(dropdownElement);
    dropdownElement.click();
    expect(component.onClickOutside).not.toHaveBeenCalled();
  });

  it('should not emit clickOutside event when clicking inside the filter-dropdown element', () => {
    spyOn(component, 'onClickOutside');
    const dropdownElement = document.createElement('div');
    dropdownElement.classList.add('filter-dropdown');
    const childElement = document.createElement('div');
    dropdownElement.appendChild(childElement);
    document.body.appendChild(dropdownElement);
    childElement.click();
    expect(component.onClickOutside).not.toHaveBeenCalled();
  });
});

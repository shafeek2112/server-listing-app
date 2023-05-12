import { Directive, ElementRef, Output, EventEmitter, HostListener } from '@angular/core';

@Directive({
  selector: '[clickOutside]'
})
export class ClickOutsideDirective {
  constructor(private elementRef: ElementRef) {}

  @Output() clickOutside = new EventEmitter<MouseEvent>();

  @HostListener('document:click', ['$event'])
  onClick(event: MouseEvent) {
    const targetElement = event.target as HTMLElement;
    const clickedInside = this.elementRef.nativeElement.contains(targetElement);
    const isDropdown = targetElement.classList.contains('filter-dropdown');
    const isDropdownChild = targetElement.closest('.filter-dropdown') !== null;
    if (!clickedInside && !isDropdown && !isDropdownChild) {
      this.clickOutside.emit(event);
    }
  }
}

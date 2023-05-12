import { Component, EventEmitter, Input, Output } from '@angular/core';

@Component({
  selector: 'app-compare',
  templateUrl: './compare.component.html',
  styleUrls: ['./compare.component.css']
})
export class CompareComponent {
  @Input() compareTwoSystems!: any[];
  @Output() closeCompareModal = new EventEmitter<void>();

  onClose() {
    this.closeCompareModal.emit();
  }
}

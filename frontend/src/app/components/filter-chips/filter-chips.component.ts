import { Component, EventEmitter, Input, OnChanges, Output, SimpleChanges } from '@angular/core';

@Component({
  selector: 'app-filter-chips',
  templateUrl: './filter-chips.component.html',
  styleUrls: ['./filter-chips.component.css']
})
export class FilterChipsComponent {
  @Input() filters: any;
  @Output() filterRemoved = new EventEmitter();
  @Output() filtersUpdated = new EventEmitter();

  get ramFilters() {
    return this.filters?.ram || [];
  }

  get storageFilter() {
    return this.filters?.storage.join(" - ") || '';
  }

  get hardiskFilter() {
    return this.filters?.hardisk;
  }

  get locationFilter() {
    return this.filters?.location;
  }

  removeRamFilter(ramFilter: string) {
    const updatedFilters = { ...this.filters };
    updatedFilters.ram = updatedFilters.ram.filter((filter: any) => filter !== ramFilter);
    this.filters = updatedFilters;
    this.filterRemoved.emit();
    this.filtersUpdated.emit(updatedFilters);
  }

  removeStorageFilter() {
    const updatedFilters = { ...this.filters };
    updatedFilters.storage = [];
    this.filters = updatedFilters;
    this.filterRemoved.emit();
    this.filtersUpdated.emit(updatedFilters);
  }

  removeHardiskFilter() {
    const updatedFilters = { ...this.filters };
    updatedFilters.hardisk = null;
    this.filters = updatedFilters;
    this.filterRemoved.emit();
    this.filtersUpdated.emit(updatedFilters);
  }

  removeLocationFilter() {
    const updatedFilters = { ...this.filters };
    updatedFilters.location = null;
    this.filters = updatedFilters;
    this.filterRemoved.emit();
    this.filtersUpdated.emit(updatedFilters);
  }
}

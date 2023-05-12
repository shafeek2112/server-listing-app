import { Component, EventEmitter, Input, OnChanges, OnInit, Output, SimpleChanges } from '@angular/core';
import { environment } from 'src/environments/environment';

interface Filter {
  ram: string[];
  storage: number[];
  hardisk: string;
  location: string;
}

@Component({
  selector: 'app-filter',
  templateUrl: './filter.component.html',
  styleUrls: ['./filter.component.css']
})
export class FilterComponent implements OnInit, OnChanges {

  // Filter Dropdown
  showStorage = false;
  showRam = false;
  showHardDisk = false;
  showLocation = false;

  closeStorage() {
    this.showStorage = false;
  }

  openStorage() {
    this.showStorage = !this.showStorage;
    //Close others
    this.closeAllExcept('storage')
  }

  openRam() {
    this.showRam = !this.showRam;
    //Close others
    this.closeAllExcept('ram')
  }

  openHardDisk() {
    this.showHardDisk = !this.showHardDisk;
    //Close others
    this.closeAllExcept('hardDisk')
  }

  openLocation() {
    this.showLocation = !this.showLocation;
    //Close others
    this.closeAllExcept('location')
  }

  closeAllExcept(type:string) {
    if(type !== 'storage') this.closeStorage();
    if(type !== 'ram') this.closeRam();
    if(type !== 'hardDisk') this.closeHardDisk();
    if(type !== 'location') this.closeLocation();
  }

  closeRam() {
    this.showRam = false;
  }

  closeHardDisk() {
    this.showHardDisk = false;
  }

  closeLocation() {
    this.showLocation = false;
  }

  // Slider
  value: number = 0;
  highValue: number = 11;
  options: any = {
    floor: 0,
    ceil: 11,
    showTicks: true,
    getSelectionBarColor: (value: number): string => {
      return '#6366f1';
    },
    getPointerColor: (value: number): string => {
      return '#6366f1';
    },
    translate: (value: number, label: string): string => {
      switch (value) {
        case 0: return '0GB';
        case 1: return '250GB';
        case 2: return '500GB';
        case 3: return '1TB';
        case 4: return '2TB';
        case 5: return '3TB';
        case 6: return '4TB';
        case 7: return '8TB';
        case 8: return '12TB';
        case 9: return '24TB';
        case 10: return '48TB';
        case 11: return '72TB';
        default: return '';
      }
    },
    getLegend: (value: number, label: string): string => {
      return label;
    }
  };

  // Filter
  @Input() filters: Filter = {
    ram: [],
    storage: [],
    hardisk: '',
    location: ''
  };
  @Output() filtersChanged = new EventEmitter<Filter>();

  public ramValues: string[] = environment.ramValues;
  public hardiskValues = environment.hardiskValues;
  public locationValues = environment.locationValues;

  ngOnInit(): void {
    // console.log(this.filters)
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['filters']) {
      this.filters.ram = this.filters.ram || [];
      this.filters.storage = this.filters.storage || [];
      this.filters.hardisk = this.filters.hardisk || '';
      this.filters.location = this.filters.location || '';
    }
  }

  onRamChange(value: string, isChecked: boolean): void {
    if (isChecked) {
      this.filters.ram.push(value);
    } else {
      const index = this.filters.ram.indexOf(value);
      if (index !== -1) {
        this.filters.ram.splice(index, 1);
      }
    }
    this.filtersChanged.emit(this.filters);
  }

  onUserChange(event: any) {
    const value = this.options.translate(event.value);
    const highValue = this.options.translate(event.highValue);
    this.filters.storage = [value, highValue]
    this.filtersChanged.emit(this.filters);
  }

  onHardiskChange(value: string) {
    this.filters.hardisk = value;
    this.filtersChanged.emit(this.filters);
  }

  onLocationChange(value: string) {
    this.filters.location = value;
    this.filtersChanged.emit(this.filters);
  }


  updateFilters(updatedFilters: any) {
    this.filters = updatedFilters;
    this.filtersChanged.emit(this.filters);
  }
}

import { HttpParams } from '@angular/common/http';
import { Component, ElementRef } from '@angular/core';
import { ActivatedRoute, Params, Router } from '@angular/router';
import { SystemService } from 'src/app/core/services/system.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-main',
  templateUrl: './main.component.html',
  styleUrls: ['./main.component.css']
})
export class MainComponent {
  public isLoading: boolean = true;
  showStorage = true;
  showRam = true;
  showHardDisk = true;
  showLocation = true;
  pagination: any = environment.pagination;
  systems:any[] = [];
  filters: any = {};
  isListView = true;
  errorMessage: string = "";
  compareTwoSystems:any[] = [];
  compareModalOpen = false;

  constructor(private systemService: SystemService, private route: ActivatedRoute, private router: Router, private el: ElementRef){ }

  ngOnInit(): void {
    this.filters = {
      ram: [],
      storage: [],
      hardisk: '',
      location: ''
    };
    //Get the page from url.
    this.route.queryParams.subscribe((params:any) => {
      const pageParam = params['page'];
      const storageParam = params['storage'];
      const ramParam = params['ram'];
      const hardiskParam = params['hardisk'];
      const locationParam = params['location'];

      if(storageParam) {
        this.filters.storage = storageParam.trim().split(",");
      }
      if(ramParam) {
        this.filters.ram = ramParam.trim().split(",");
      }
      if(pageParam) this.pagination.page = pageParam;
      if(hardiskParam) this.filters.hardisk = hardiskParam;
      if(locationParam) this.filters.location = locationParam;
    });
    this.getSystems();
  }

  updateUrlParams(filters: any, page: number) {
    let params: Params = {};

    // Add ram filter
    if (this.filters.ram.length > 0) {
      params['ram'] = this.filters.ram.join(',');
    }

    // Add storage filter
    if (this.filters.storage.length > 0) {
      params['storage'] = this.filters.storage.join(',');
    }

    // Add location filter
    if (this.filters.location !== '') {
      params['location'] = this.filters.location;
    }

    // Add hardisk filter
    if (this.filters.hardisk !== '') {
      params['hardisk'] = this.filters.hardisk;
    }

    // Add page number
    params['page'] = this.pagination.page;

    // Remove empty filters
    Object.keys(params).forEach(key => {
      if (Array.isArray(params[key]) && params[key].length === 0) {
        delete params[key];
      } else if (params[key] === '') {
        delete params[key];
      }
    });

    // Update URL
    this.router.navigate([], {queryParams: params});
  }

  getSystems() {
    this.systemService.getSystems(this.pagination.page, this.filters).subscribe({
      next: (data:any) => {
        this.systems = data.data;
        this.pagination.totalPages = data.totalPages;
        this.pagination.startNumber = data.startNumber;
        this.pagination.endNumber = data.endNumber;
        this.pagination.totalRecords = data.totalRecords;
      },
      error: (error:any) => {
        console.log("Error", error);
        // Handle error, e.g., show a message or update the UI
      },
      complete: () => console.log("Complete")
    });
  }

  onNextPage() {
    if (this.pagination.page < this.pagination.totalPages) {
      this.pagination.page++;
      this.updateUrlParams(this.filters, this.pagination.page);
      this.getSystems();
    }
  }

  onPrevPage() {
    if (this.pagination.page > 1) {
      this.pagination.page--;
      this.updateUrlParams(this.filters, this.pagination.page);
      this.getSystems();
    }
  }


  // Filter
  onFiltersChanged(updatedFilters: any) {
    this.filters = updatedFilters;
    this.pagination.page = 1;
    this.updateUrlParams(this.filters, this.pagination.page);
    this.getSystems();
  }

  toggleViewMode() {
    this.isListView = !this.isListView;
  }

  // Compare
  onCompare(value: any, isChecked: boolean): void {
    if (isChecked && this.compareTwoSystems.length < 2) {
      this.compareTwoSystems.push(value);
    } else {
      const index = this.compareTwoSystems.findIndex(system => system.id === value.id);
      if (index !== -1) {
        this.compareTwoSystems.splice(index, 1);
      }
    }

    if (this.compareTwoSystems.length === 2) {
      this.onOpenCompareModal(this.compareTwoSystems);
    }
  }

  onOpenCompareModal(systems: any[]) {
    // Add code to populate the selectedItems array
    this.compareModalOpen = true;
  }

  onCloseCompareModal() {
    const checkboxes = this.el.nativeElement.querySelectorAll('.compare-check input[type=checkbox]');
    checkboxes.forEach(function(checkbox: any) {
      checkbox.checked = false;
    });
    this.compareTwoSystems = [];
    this.compareModalOpen = false;
  }
}

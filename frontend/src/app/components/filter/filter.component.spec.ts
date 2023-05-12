import { ComponentFixture, TestBed } from '@angular/core/testing';
import { FormsModule } from '@angular/forms';
import { FilterComponent } from './filter.component';
import { NgxSliderModule } from 'ngx-slider-v2';

describe('FilterComponent', () => {
  let component: FilterComponent;
  let fixture: ComponentFixture<FilterComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormsModule, NgxSliderModule],
      declarations: [ FilterComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(FilterComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should update filters on RAM change', () => {
    spyOn(component.filtersChanged, 'emit');
    component.onRamChange('2GB', true);
    expect(component.filters.ram).toEqual(['2GB']);
    expect(component.filtersChanged.emit).toHaveBeenCalledWith(component.filters);
  });

  it('should update filters on hard disk change', () => {
    spyOn(component.filtersChanged, 'emit');
    component.onHardiskChange('SAS');
    expect(component.filters.hardisk).toEqual('SAS');
    expect(component.filtersChanged.emit).toHaveBeenCalledWith(component.filters);
  });

  it('should update filters on location change', () => {
    spyOn(component.filtersChanged, 'emit');
    component.onLocationChange('Singapore');
    expect(component.filters.location).toEqual('Singapore');
    expect(component.filtersChanged.emit).toHaveBeenCalledWith(component.filters);
  });

});

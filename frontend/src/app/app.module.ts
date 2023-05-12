import { CUSTOM_ELEMENTS_SCHEMA, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { HeaderComponent } from './components/header/header.component';
import { MainComponent } from './components/main/main.component';
import { NotfoundComponent } from './components/notfound/notfound.component';
import { FilterComponent } from './components/filter/filter.component';
import { ClickOutsideDirective } from './directives/click-outside.directive';
import { NgxSliderModule } from 'ngx-slider-v2';
import { FilterChipsComponent } from './components/filter-chips/filter-chips.component';
import { ErrorAlertComponent } from './components/error-alert/error-alert.component';
import { CompareComponent } from './components/compare/compare.component';

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    MainComponent,
    NotfoundComponent,
    FilterComponent,
    ClickOutsideDirective,
    FilterChipsComponent,
    ErrorAlertComponent,
    CompareComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    BrowserAnimationsModule,
    HttpClientModule,
    NgxSliderModule
  ],
  providers: [],
  bootstrap: [AppComponent],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule { }

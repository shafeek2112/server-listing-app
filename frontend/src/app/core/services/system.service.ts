import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { retry, catchError } from 'rxjs/operators';
import { Observable, throwError } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class SystemService {

  constructor(private http: HttpClient) { }

  getSystems(page: number, filters?: any){
    let params = new HttpParams()
    .set('page', page.toString())
    if (filters) {
      for (const key in filters) {
        if (filters.hasOwnProperty(key)) {
          params = params.set(key, filters[key]);
        }
      }
    }

    return this.http.get(`${environment.apiUrl}/servers`, { params });
  }
}

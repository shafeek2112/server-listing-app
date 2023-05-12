import { TestBed } from '@angular/core/testing';
import { HttpClientTestingModule, HttpTestingController } from '@angular/common/http/testing';
import { SystemService } from './system.service';
import { environment } from 'src/environments/environment';


describe('SystemService', () => {
  let service: SystemService;
  let httpMock: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [SystemService]
    });
    service = TestBed.inject(SystemService);
    httpMock = TestBed.inject(HttpTestingController);
  });

  afterEach(() => {
    httpMock.verify();
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should return a list of systems', () => {
    const dummySystems = [
      { id: 1, name: 'System 1', ram: 8, storage: 256, location: 'USA', price: 1000 },
      { id: 2, name: 'System 2', ram: 16, storage: 512, location: 'Europe', price: 1500 }
    ];
    const page = 1;
    const filters = { location: 'USA' };

    service.getSystems(page, filters).subscribe(systems => {
      expect(systems).toEqual(dummySystems);
    });

    const req = httpMock.expectOne(`${environment.apiUrl}/servers?page=${page}&location=${filters.location}`);
    expect(req.request.method).toBe('GET');
    req.flush(dummySystems);
  });

  it('should handle errors', () => {
    const page = 1;
    const filters = { location: 'USA' };

    service.getSystems(page, filters).subscribe(
      systems => fail('expected an error'),
      error => expect(error.status).toBe(404)
    );

    const req = httpMock.expectOne(`${environment.apiUrl}/servers?page=${page}&location=${filters.location}`);
    expect(req.request.method).toBe('GET');
    req.flush('Not found', { status: 404, statusText: 'Not found' });
  });
});

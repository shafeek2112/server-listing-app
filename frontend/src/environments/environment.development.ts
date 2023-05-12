export const environment = {
  production: false,
  env: 'dev',
  apiUrl: 'http://localhost:8000/api',
  pagination: { page: 1, totalPages: 10, perPage: 10, startNumber: 1, endNumber: 10, totalRecords: 10 },
  ramValues: ['2GB', '4GB', '8GB', '12GB', '16GB', '24GB', '32GB', '48GB', '64GB', '96GB'],
  hardiskValues: ['SAS', 'SATA', 'SSD'],
  locationValues: ['AmsterdamAMS-01', 'Washington D.C.WDC-01', 'San FranciscoSFO-12', 'SingaporeSIN-11', 'DallasDAL-10', 'FrankfurtFRA-10', 'Hong KongHKG-10']
};

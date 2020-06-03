import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ServiceWorkerModule } from '@angular/service-worker';
import { environment } from '../environments/environment';
import { FormsModule } from '@angular/forms';
import { HeaderComponent } from './components/header/header.component';
import { FooterComponent } from './components/footer/footer.component';
import { HomeComponent } from './components/pages/home/home.component';
import { QrReaderComponent } from './components/pages/qr-reader/qr-reader.component';

import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { PERFECT_SCROLLBAR_CONFIG } from 'ngx-perfect-scrollbar';
import { PerfectScrollbarConfigInterface } from 'ngx-perfect-scrollbar';
import { DBLettersService } from './services/dbletters.service';
import { HttpClientModule } from '@angular/common/http';
import { StorageService } from './services/storage.service';

import { NgxIndexedDBModule, DBConfig, NgxIndexedDBService } from 'ngx-indexed-db';

const DEFAULT_PERFECT_SCROLLBAR_CONFIG: PerfectScrollbarConfigInterface = {
  suppressScrollX: true,
  suppressScrollY: false
};

const dbConfig: DBConfig  = {
  name: 'db',
  version: 1,
  objectStoresMeta: [{
    store: 'ids',
    storeConfig: { keyPath: 'id', autoIncrement: true },
    storeSchema: [
      { name: 'ider', keypath: 'ider', options: { unique: true } },
    ]
  }]
};

@NgModule({
  declarations: [
    AppComponent,
    HeaderComponent,
    FooterComponent,
    HomeComponent,
    QrReaderComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ServiceWorkerModule.register('ngsw-worker.js', { enabled: environment.production }),
    FormsModule,
    HttpClientModule,

    NgxIndexedDBModule.forRoot(dbConfig),

    PerfectScrollbarModule,
  ],
  providers: [
    { provide: PERFECT_SCROLLBAR_CONFIG, useValue: DEFAULT_PERFECT_SCROLLBAR_CONFIG },
    DBLettersService,
    NgxIndexedDBService,
    StorageService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }

import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { DashboardComponent } from './dashboard/dashboard.component';
import { CreateNewLetterComponent } from './components/create-new-letter/create-new-letter.component';
import { LetterFormComponent } from './components/letter-form/letter-form.component';

import { PerfectScrollbarModule } from 'ngx-perfect-scrollbar';
import { TrackerComponent } from './components/tracker/tracker.component';

@NgModule({
  declarations: [
    AppComponent,
    DashboardComponent,
    CreateNewLetterComponent,
    LetterFormComponent,
    TrackerComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,

    PerfectScrollbarModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }

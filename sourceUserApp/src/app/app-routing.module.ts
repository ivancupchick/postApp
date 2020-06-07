import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { DashboardComponent } from './dashboard/dashboard.component';
import { CreateNewLetterComponent } from './components/create-new-letter/create-new-letter.component';
import { TrackerComponent } from './components/tracker/tracker.component';


const routes: Routes = [
  { path: '', pathMatch: 'full', redirectTo: 'create-letter'},
  { path: 'dashboard', component: DashboardComponent },
  { path: 'create-letter', component: CreateNewLetterComponent },
  { path: 'tracker', component: TrackerComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { useHash: true })],
  exports: [RouterModule]
})
export class AppRoutingModule { }

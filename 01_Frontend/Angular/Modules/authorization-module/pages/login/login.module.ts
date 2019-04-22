import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Routes, RouterModule } from '@angular/router';

import { IonicModule } from '@ionic/angular';

import { LoginPage } from './login.page';
import {HeaderComponentModule} from '../../components/header/header.component.module';
import {BackgroundComponentModule} from '../../components/background/background.component.module';
import {RegisterHereComponentModule} from '../../components/register-here/register-here.component.module';
import {FooterComponentModule} from '../../components/footer/footer.component.module';
import {TranslateModule} from '@ngx-translate/core';

const routes: Routes = [
  {
    path: '',
    component: LoginPage
  }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    RouterModule.forChild(routes),
    HeaderComponentModule,
    BackgroundComponentModule,
    RegisterHereComponentModule,
    FooterComponentModule,
    TranslateModule
  ],
  declarations: [LoginPage]
})
export class LoginPageModule {}

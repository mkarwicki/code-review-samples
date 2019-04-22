/**
 * Module with user login with email, facebook, google, reset password actions, guard.
 * Pages: Welcome Page, Login Page, Reset password page.
 */
import { IonicModule } from '@ionic/angular';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AuthorizationRouterModule } from './authorization.router.module';
import { HeaderComponentModule } from './components/header/header.component.module';
import { BackgroundComponentModule } from './components/background/background.component.module';
import { RegisterHereComponentModule } from './components/register-here/register-here.component.module';
import { FooterComponentModule } from './components/footer/footer.component.module';
import { AuthorizationService } from './services/authorization.service';
import { LoaderService } from '../../shared/services/utils/loader.service';
import { ToastService } from '../../shared/services/utils/toast.service';
import { TextService } from '../../shared/services/utils/text.service';
import {EmailLoginCommand} from './services/authorization-commands/email-login-command';
import {FacebookLoginCommand} from './services/authorization-commands/facebook-login-command';
import {Facebook} from '@ionic-native/facebook/ngx';
import {GoogleLoginCommand} from './services/authorization-commands/google-login-command';


@NgModule({
  imports: [
    IonicModule,
    CommonModule,
    FormsModule,
    AuthorizationRouterModule,
    HeaderComponentModule,
    BackgroundComponentModule,
    RegisterHereComponentModule,
    FooterComponentModule,
  ],
  providers: [
    AuthorizationService,
    LoaderService,
    ToastService,
    TextService,
    Facebook,
    EmailLoginCommand,
    FacebookLoginCommand,
    GoogleLoginCommand
  ],
  declarations: [],
  entryComponents: [],
  exports: [

  ]
})
export class AuthorizationModule {}

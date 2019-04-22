import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {WelcomeGuard} from './services/welcome-guard.service';
import {NavigationConfig as nc} from '../../../config/navigation.config';



const routes: Routes = [
  {
    path: nc.welcome.slug,
    loadChildren: './pages/welcome/welcome.module#WelcomePageModule',
    canActivate: [WelcomeGuard]
  },
  {
    path: nc.login.slug,
    loadChildren: './pages/login/login.module#LoginPageModule',
    canActivate: [WelcomeGuard]
  },
  {
    path: nc.forgot_password.slug,
    loadChildren: './pages/forgot-password/forgot-password.module#ForgotPasswordPageModule',
    canActivate: [WelcomeGuard]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  declarations: [],
  exports: [RouterModule]
})
export class AuthorizationRouterModule {
}

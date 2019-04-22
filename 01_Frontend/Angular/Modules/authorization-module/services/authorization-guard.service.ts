import {ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot} from '@angular/router';
import {Injectable} from '@angular/core';
import {AppService} from '../../../shared/services/app.service';
import {NavigationConfig as nc} from '../../../../config/navigation.config';

@Injectable()
export class AuthorizationGuard implements CanActivate {


  constructor(private app: AppService,  private router: Router) {
  }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean  {
     if (!this.app.user.isActive) {
       this.router.navigate([nc.welcome.path]);
     }
     return this.app.user.isActive;
  }


}

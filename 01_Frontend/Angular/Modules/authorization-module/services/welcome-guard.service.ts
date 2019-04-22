import {ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot} from '@angular/router';
import {Injectable} from '@angular/core';
import {AppService} from '../../../shared/services/app.service';

@Injectable()
export class WelcomeGuard implements CanActivate {

  constructor(
    private app: AppService,
    private router: Router
  ) {
  }


  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    if (this.app.user.isActive) {
      this.router.navigate([this.app.nav.listing_summary.path]);
    }
    return !this.app.user.isActive;
  }


}

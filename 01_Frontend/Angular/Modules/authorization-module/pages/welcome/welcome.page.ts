import {Component, OnInit} from '@angular/core';
import {AppService} from '../../../../shared/services/app.service';
import {AuthorizationService} from '../../services/authorization.service';


@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.page.html',
  styleUrls: [
    '../../styles/authorization.module.global.scss',
    './welcome.page.scss'
  ],
})
export class WelcomePage implements OnInit {


  constructor(public app: AppService, public auth: AuthorizationService) {
  }

  ngOnInit() {

  }

  async loginWithFacebook() {
    await this.auth.loginUserWithFacebook();
  }

  async loginWithGoogle() {
    await this.auth.loginUserWithGoogle();
  }


}

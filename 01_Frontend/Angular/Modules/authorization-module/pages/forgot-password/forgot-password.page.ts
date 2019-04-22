import { Component, OnInit } from '@angular/core';
import {AppService} from '../../../../shared/services/app.service';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: [
    '../../styles/authorization.module.global.scss',
    './forgot-password.page.scss'
  ],
})
export class ForgotPasswordPage implements OnInit {

  constructor(public app: AppService ) { }

  ngOnInit() {
  }

}

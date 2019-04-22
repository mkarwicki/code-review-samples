import { Component, OnInit } from '@angular/core';
import {AppService} from '../../../../shared/services/app.service';
import {environment} from '../../../../../environments/environment';

@Component({
  selector: 'app-register-here',
  templateUrl: './register-here.component.html',
  styleUrls: ['./register-here.component.scss']
})
export class RegisterHereComponent implements OnInit {
  private readonly staticNav = environment.static_nav;
  public registerHereLink: string;

  constructor(public app: AppService) {
    this.registerHereLink = this.staticNav.register.path;


  }

  ngOnInit() {
  }

}

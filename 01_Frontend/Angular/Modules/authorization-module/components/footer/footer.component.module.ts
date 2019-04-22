import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';

import { FooterComponent } from './footer.component';
import {TranslateModule} from '@ngx-translate/core';

@NgModule({
    imports: [
        CommonModule,
        IonicModule,
        TranslateModule
    ],
    declarations: [FooterComponent],
    exports: [FooterComponent]
})
export class FooterComponentModule {}

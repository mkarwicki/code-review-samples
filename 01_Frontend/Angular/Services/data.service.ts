import {Injectable} from '@angular/core';
import {Http, Response} from '@angular/http';
import {map} from 'rxjs/operators';
import {EuromillonariaenDataModel} from "./models/euromillonariaen-data.model";
import {LottoDataModel} from "./models/lotto-data.model";
import {EurojackpotDataModel} from "./models/eurojackpot-data.model";

@Injectable({
    providedIn: 'root'
})
export class DataService {
    http: Http;
    private euromillonariaenData: EuromillonariaenDataModel;
    private lottoData: LottoDataModel;
    private eurojackpotData: EurojackpotDataModel;

    constructor(http: Http,) {
        this.http = http;
    }

    hideOverlay() {
        const el = document.getElementById('preloader');
        el.classList.remove('active');
        const body = document.getElementsByTagName('BODY')[0];
        body.classList.add('dom-loaded');
    }

    getDynamicData() {
        return new Promise((resolve, reject) => {
        this.http
            .get('http://datascraper.it.consultingandmanagement.de/api.php')
            .pipe(map((response: Response) => {
                const data = response.json();
                return data;
            }))
            .subscribe((data) => {

                this.euromillonariaenData = data.euromilonear;
                this.lottoData = data.lotto;
                this.eurojackpotData = data.eurojackpot;


                this.hideOverlay();
                resolve(true);
            });
        });
    }

    getEuromillonariaenData(): EuromillonariaenDataModel {
        return this.euromillonariaenData;
    }

    getLottoData(): LottoDataModel {
        return this.lottoData;
    }

    getEurojackpotData(): EurojackpotDataModel {
        return this.eurojackpotData;
    }
}

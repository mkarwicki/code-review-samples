import { Injectable } from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import { Subject } from 'rxjs/Subject';
import 'rxjs/add/operator/map';
import {SectionModel} from "./models/section.model";
import {Meta, Title} from "@angular/platform-browser";
import {Router} from "@angular/router";

@Injectable()
export class SectionsService {
  http: Http;
  private sections: SectionModel[] = [];
  private arthouseProjectElement: any;
  private aboutInvestmentData: any;
  private ourVisionData: any;
  private visionStepsData: any;
  private aboutInvestmentCategoriesData: any;
  private yourApartmentsData: any;
  private businessOptionsData: any;
  private officeSubMetaData: any;
  private lifestyleData: any;
  private freeTimeData: any;
  private historyData: any;
  private contactData: any;
  private galleryData: any;
  private socialData: any;
  public metadata: any;
  activeSectionId = 1;
  deactivateHistoryChange = false;



  constructor(http: Http, private meta: Meta, private title: Title) {
      this.http = http;
  }

  getAllSections() {
      return this.sections;
  }

  goToSection(sectionID) {
      const el = document.getElementById('section-id-' + sectionID);
      if (el) {
        el.scrollIntoView({ block: 'start', behavior: 'smooth'});
      }
  }

  goToSectionInstant(sectionID) {
      const el = document.getElementById('section-id-' + sectionID);
      if(el) {
        el.scrollIntoView({ block: 'start', behavior: 'instant'});
      }
  }

  goToSectionByFragment(fragment, animationType) {
      let sectionID;
      for (let i = 0; i < this.sections.length; i++) {
          const navElement = this.sections[i];
          if(navElement.fragment === fragment) {
              sectionID = navElement.sectionID;
          }
      }
      const el = document.getElementById('section-id-' + sectionID);
      if (el) {
        el.scrollIntoView({ block: 'start', behavior: animationType});
        this.updateActiveSectionID(sectionID);
      }
  }

  goToNextSection(){
      for (let i = 0; i < this.sections.length; i++) {
          let navElement = this.sections[i];
          if( navElement.currentNavigation == true) {
              if(this.sections.length > (i+1)){
                this.goToSection(this.sections[i+1].sectionID);
                return;
              }
          }
      }
  }

  goToPrevSection(){
      for (let i = 0; i < this.sections.length; i++) {
          let navElement = this.sections[i];
          if(navElement.currentNavigation == true){
              if((i-1) >= 0) {
                this.goToSection(this.sections[i-1].sectionID);
                return;
              }
          }
      }
  }

  updateActiveSectionID(id) {
      this.activeSectionId = id;
      this.sections.map((section) => {
          if (section.sectionID != id) {
            section.currentNavigation = false;
          } else {
            section.currentNavigation = true;
              //this.title.setTitle(section.meta.title);
              //this.meta.updateTag({name: 'description', content: section.meta.description});
          }
      });
  }


  updateMeta(meta) {
      this.title.setTitle(meta.title);
      this.meta.updateTag({name: 'description', content: meta.description});
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
              .get('xxx')
              .map((response: Response) => {
                const data = response.json();
                return data;
              })
              .subscribe((data) => {
                  this.sections = data.sections;
                  this.arthouseProjectElement = data.arthouseProjectElement;
                  this.aboutInvestmentData = data.aboutInvestmentData;
                  this.ourVisionData = data.ourVisionData;
                  this.visionStepsData = data.visionStepsData;
                  this.aboutInvestmentCategoriesData = data.aboutInvestmentCategoriesData;
                  this.yourApartmentsData = data.yourApartmentsData;
                  this.businessOptionsData = data.businessOptionsData;
                  this.officeSubMetaData = data.officeSubMetaData;
                  this.lifestyleData = data.lifestyleData;
                  this.freeTimeData = data.freeTime;
                  this.historyData = data.historyData;
                  this.contactData = data.contactData;
                  this.galleryData = data.galleryData;
                  this.socialData = data.socialData;
                  this.metadata = data.meta;
                  this.hideOverlay();
                  resolve(true);
              });
      });
    }

    getArhouseProjectElements() {
      return this.arthouseProjectElement;
    }


    getAboutInvestmentData() {
      return this.aboutInvestmentData;
    }

    getOurVisionData() {
      return this.ourVisionData;

    }

    getOurVisionStepsData() {
    return this.visionStepsData;

    }

    getAboutInvestmentCategoriesData() {
      return this.aboutInvestmentCategoriesData;
    }

    getYourApartmentData(){
      return this.yourApartmentsData;
    }

    getBusinessOptionsData() {
        return this.businessOptionsData;
    }


    getOfficeSubMetaData(){
      return this.officeSubMetaData;
    }



    getLifestyleData() {
        return this.lifestyleData;
    }

    getHistoryData() {
        return this.historyData;
    }
    getFreeTimeData() {
        return this.freeTimeData;
    }

    getContactData() {
        return this.contactData;
    }

    getGalleryData() {
        return this.galleryData;
    }


    getSocialData(): any {
        return this.socialData;
    }


}

import { environment } from './../environments/environment';
import { environment } from '../environments/environment';
import { Component } from '@angular/core';
import { FetchImagesService } from './fetch-images.service';
import {NgbModal, ModalDismissReasons} from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'app';
  images : any;
  filterIsChecked : boolean;
  imageSelectedId : number;
  imageName : string;
  imageSysName : string;
  environment = environment;

  constructor(public FetchImagesService: FetchImagesService, public modalService: NgbModal) {
    this.filterIsChecked = true;
    this.FetchImagesService.fetchImages().subscribe((data:any) => {
      this.images = data;
    });

  }

  filterImages(type:string) {
    this.imageSelectedId = undefined;
    this.filterIsChecked = type === "all";
    this.FetchImagesService.fetchImages(type).subscribe((data:any) => {
      this.images = data;
    });
  }

  pickImage(image:any, e) {
    e.stopPropagation();
    this.imageSelectedId = image.id;
    this.imageName = image.file_original_name;
    this.imageSysName = image.file_system_name;
  }

  confirmDelete(content) {
    this.modalService.open(content).result.then((result) => {}, (reason) => {});
  }

  deleteImage() {
    this.FetchImagesService.deleteImage(this.imageSelectedId).subscribe((data:any) => {
      console.log(data);
      this.filterImages("all");
    });
  }

  restoreImage() {
    this.FetchImagesService.restoreImage(this.imageSelectedId).subscribe((data:any) => {
      console.log(data);
      this.filterImages("all");
    });
  }

  downloadImage() {
    this.FetchImagesService.downloadImage(this.imageSelectedId).subscribe((data:any) => {
      window.location = data._body;
    });
    // var xhr = new XMLHttpRequest();
    // var formData = new FormData();
    // xhr.open('GET', this.environment.imagesUrl + this.imageSysName, true);
    // xhr.responseType = 'blob';
    // xhr.withCredentials = true;

    // xhr.onload = (e) =>
    // {
        // var blob = new Blob([xhr.response], { type: "image/*"});
        // var objectUrl = URL.createObjectURL(blob);
        // var anchor = document.createElement("a");
        // anchor.download = this.imageName;
        // anchor.href = objectUrl;
        // anchor.click();
    // }

    // xhr.send();
  }

  unselectImage(e) {
    this.imageSelectedId = undefined;
  }

}

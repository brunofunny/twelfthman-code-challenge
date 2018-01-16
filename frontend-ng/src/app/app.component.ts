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
  imageUrl : string;
  imagePath = 'assets/imgs/';

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
    this.imageUrl = image.image;
  }

  confirmDelete(content) {
    this.modalService.open(content).result.then((result) => {
    }, (reason) => {
    
    });
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
    var xhr = new XMLHttpRequest();
    var formData = new FormData();
    xhr.open('GET', this.imagePath + this.imageUrl, true);
    xhr.responseType = 'blob';

    xhr.onload = (e) =>
    {
        var blob = new Blob([xhr.response], { type: "image/*"});
        var objectUrl = URL.createObjectURL(blob);
        var anchor = document.createElement("a");
        anchor.download = this.imageUrl;
        anchor.href = objectUrl;
        anchor.click();
    }

    xhr.send();
  }

  unselectImage(e) {
    this.imageSelectedId = undefined;
  }

}

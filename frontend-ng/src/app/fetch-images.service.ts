import { Injectable } from '@angular/core';
import { Http, Response } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/do';

@Injectable()
export class FetchImagesService {

  constructor(public http: Http) { }

  fetchImages(type?:string): Observable<any> {
    type = type || "all";
    return this.http.get('http://localhost:8000/api/images/' + type)
        .map((response: Response) => <any>response.json());
        // .do(data => console.log('All: ' + JSON.stringify(data)));
  }

  deleteImage(id:number): Observable<any> {
    return this.http.delete('http://localhost:8000/api/images/' + id);
  }

  restoreImage(id:number): Observable<any> {
    return this.http.get('http://localhost:8000/api/images-restore/' + id);
  }

}

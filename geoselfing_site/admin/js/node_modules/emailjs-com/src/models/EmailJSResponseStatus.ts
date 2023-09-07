export class EmailJSResponseStatus {

  public status: number;
  public text: string;

  constructor(httpResponse: XMLHttpRequest) {
    this.status = httpResponse.status;
    this.text = httpResponse.responseText;
  }
}

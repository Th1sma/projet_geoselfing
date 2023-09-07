export class UI {

  private static readonly PROGRESS: string = 'emailjs-sending';
  private static readonly DONE: string = 'emailjs-success';
  private static readonly ERROR: string = 'emailjs-error';

  public static clearAll(form: HTMLFormElement): void {
    form.classList.remove(this.PROGRESS);
    form.classList.remove(this.DONE);
    form.classList.remove(this.ERROR);
  }

  public static progressState(form: HTMLFormElement): void {
    this.clearAll(form);
    form.classList.add(this.PROGRESS);
  }

  public static successState(form: HTMLFormElement): void {
    form.classList.remove(this.PROGRESS);
    form.classList.add(this.DONE);
  }

  public static errorState(form: HTMLFormElement): void {
    form.classList.remove(this.PROGRESS);
    form.classList.add(this.ERROR);
  }

}

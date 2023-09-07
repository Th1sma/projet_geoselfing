import {UI} from './UI';

test ('append emailjs-sending class to form', () => {
  let form: HTMLFormElement = <HTMLFormElement>document.createElement('FORM');
  UI.progressState(form);

  expect(form.classList.item(0)).toBe((<any>UI).PROGRESS);
});

test ('append emailjs-error class to form', () => {
  let form: HTMLFormElement = <HTMLFormElement>document.createElement('FORM');
  UI.errorState(form);

  expect(form.classList.item(0)).toBe((<any>UI).ERROR);
});

test ('append emailjs-success class to form', () => {
  let form: HTMLFormElement = <HTMLFormElement>document.createElement('FORM');
  UI.successState(form);

  expect(form.classList.item(0)).toBe((<any>UI).DONE);
});

test ('clear all classes from form', () => {
  let form: HTMLFormElement = <HTMLFormElement>document.createElement('FORM');
  UI.successState(form);
  UI.clearAll(form);

  expect(form.classList.length).toBe(0);
});

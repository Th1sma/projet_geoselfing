import {EmailJSResponseStatus} from './models/EmailJSResponseStatus';
import {UI} from './services/ui/UI';

let _userID: string = null;
let _origin: string = 'https://api.emailjs.com';

function sendPost(url: string, data: string | FormData, headers: Object = {}): Promise<EmailJSResponseStatus> {
  return new Promise((resolve, reject) => {
    let xhr: XMLHttpRequest = new XMLHttpRequest();

    xhr.addEventListener('load', (event) => {
      let responseStatus: EmailJSResponseStatus = new EmailJSResponseStatus(<XMLHttpRequest>event.target);
      if (responseStatus.status === 200 || responseStatus.text === 'OK') {
        resolve(responseStatus);
      } else {
        reject(responseStatus);
      }
    });

    xhr.addEventListener('error', (event) => {
      reject(new EmailJSResponseStatus(<XMLHttpRequest>event.target));
    });

    xhr.open('POST', url, true);

    for (let key in headers) {
        xhr.setRequestHeader(key, headers[key]);
    }

    xhr.send(data);
  });
}

function appendGoogleCaptcha(templatePrams?: Object): Object {
  let element: HTMLInputElement =
    document && document.getElementById('g-recaptcha-response') as HTMLInputElement;

  if (element && element.value) {
    templatePrams['g-recaptcha-response'] = element.value;
  }

  element = null;
  return templatePrams;
}

function fixIdSelector(selector: string): string {
  if (selector[0] !== '#' && selector[0] !== '.') {
    return '#' + selector;
  }

  return selector;
}

/**
 * Initiation
 * @param {string} userID - set the EmailJS user ID
 * @param {string} origin - set the EmailJS origin
 */
export function init(userID: string, origin?: string): void {
  _userID = userID;
  _origin = origin || 'https://api.emailjs.com';
}

/**
 * Send a template to the specific EmailJS service
 * @param {string} serviceID - the EmailJS service ID
 * @param {string} templateID - the EmailJS template ID
 * @param {Object} templatePrams - the template params, what will be set to the EmailJS template
 * @param {string} userID - the EmailJS user ID
 * @returns {Promise<EmailJSResponseStatus>}
 */
export function send(serviceID: string, templateID: string, templatePrams?: Object, userID?: string): Promise<EmailJSResponseStatus> {
  let params: Object = {
    lib_version: '<<VERSION>>',
    user_id: userID || _userID,
    service_id: serviceID,
    template_id: templateID,
    template_params: appendGoogleCaptcha(templatePrams)
  };

  return sendPost(_origin + '/api/v1.0/email/send', JSON.stringify(params), {
    'Content-type': 'application/json'
  });
}

/**
 * Send a form the specific EmailJS service
 * @param {string} serviceID - the EmailJS service ID
 * @param {string} templateID - the EmailJS template ID
 * @param {string | HTMLFormElement} form - the form element or selector
 * @param {string} userID - the EmailJS user ID
 * @returns {Promise<EmailJSResponseStatus>}
 */
export function sendForm(
  serviceID: string,
  templateID: string,
  form: string | HTMLFormElement,
  userID?: string
): Promise<EmailJSResponseStatus> {
  if (typeof form === 'string') {
    form = document.querySelector<HTMLFormElement>(fixIdSelector(form));
  }

  if (!form || form.nodeName !== 'FORM') {
    throw 'Expected the HTML form element or the style selector of form';
  }

  UI.progressState(form);
  let formData: FormData = new FormData(form);
  formData.append('lib_version', '<<VERSION>>');
  formData.append('service_id', serviceID);
  formData.append('template_id', templateID);
  formData.append('user_id', userID || _userID);

  return sendPost(_origin + '/api/v1.0/email/send-form', formData)
    .then((response) => {
      UI.successState(<HTMLFormElement>form);
      return response;
    }, (error) => {
      UI.errorState(<HTMLFormElement>form);
      return Promise.reject(error);
    });
}

export {EmailJSResponseStatus};

export default {
  init,
  send,
  sendForm
};

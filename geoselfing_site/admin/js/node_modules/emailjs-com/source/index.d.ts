import { EmailJSResponseStatus } from './models/EmailJSResponseStatus';
/**
 * Initiation
 * @param {string} userID - set the EmailJS user ID
 * @param {string} origin - set the EmailJS origin
 */
export declare function init(userID: string, origin?: string): void;
/**
 * Send a template to the specific EmailJS service
 * @param {string} serviceID - the EmailJS service ID
 * @param {string} templateID - the EmailJS template ID
 * @param {Object} templatePrams - the template params, what will be set to the EmailJS template
 * @param {string} userID - the EmailJS user ID
 * @returns {Promise<EmailJSResponseStatus>}
 */
export declare function send(serviceID: string, templateID: string, templatePrams?: Object, userID?: string): Promise<EmailJSResponseStatus>;
/**
 * Send a form the specific EmailJS service
 * @param {string} serviceID - the EmailJS service ID
 * @param {string} templateID - the EmailJS template ID
 * @param {string | HTMLFormElement} form - the form element or selector
 * @param {string} userID - the EmailJS user ID
 * @returns {Promise<EmailJSResponseStatus>}
 */
export declare function sendForm(serviceID: string, templateID: string, form: string | HTMLFormElement, userID?: string): Promise<EmailJSResponseStatus>;
export { EmailJSResponseStatus };
declare const _default: {
    init: typeof init;
    send: typeof send;
    sendForm: typeof sendForm;
};
export default _default;

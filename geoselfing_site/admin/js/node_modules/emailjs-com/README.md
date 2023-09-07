# EmailJS-COM
SDK for [EmailJS.com](https://www.emailjs.com) users.
\
Use you EmailJS account for sending emails.

## Intro
EmailJS helps sending emails using client side technologies only. No server is required – just connect EmailJS to one of the supported email services, create an email template, and use our Javascript library to trigger an email.

## Quick Start

Install EmailJS SDK using [npm](https://www.npmjs.com/):

``` bash
$ npm install emailjs-com --save
```

Using [bower](https://bower.io/):
``` bash
$ bower install emailjs-com --save
```

Or manually: 

``` html
<script type='text/javascript' src='https://cdn.jsdelivr.net/npm/emailjs-com@2/dist/email.min.js'></script>
<script type='text/javascript'>
   (function(){
      emailjs.init('<YOUR USER ID>');
   })();
</script>
```

## Documentation

Documentation is available at [https://www.emailjs.com/docs](https://www.emailjs.com/docs)

## Examples

__send email__

``` js
var templateParams = {
    name: 'James',
    notes: 'Check this out!'
};

emailjs.send('<YOUR SERVICE ID>','<YOUR TEMPLATE ID>', templateParams)
	.then(function(response) {
	   console.log('SUCCESS!', response.status, response.text);
	}, function(err) {
	   console.log('FAILED...', err);
	});
```

__send form__

``` js
emailjs.sendForm('<YOUR SERVICE ID>','<YOUR TEMPLATE ID>', '#myForm')
	.then(function(response) {
	   console.log('SUCCESS!', response.status, response.text);
	}, function(err) {
	   console.log('FAILED...', err);
	});
```

__Angular X / VueJS / ReactJS__
``` js
import emailjs from 'emailjs-com';

const templateParams = {
    name: 'James',
    notes: 'Check this out!'
};

emailjs.send('<YOUR SERVICE ID>','<YOUR TEMPLATE ID>', templateParams, '<YOUR USER ID>')
	.then((response) => {
	   console.log('SUCCESS!', response.status, response.text);
	}, (err) => {
	   console.log('FAILED...', err);
	});
```

## License

[MIT](./LICENSE)

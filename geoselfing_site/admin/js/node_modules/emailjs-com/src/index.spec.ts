import emailjs from './index';

it('should send method and fail', (done) => {
  emailjs.send('test', 'test')
    .then((resolve) => {
      expect(resolve).toBeUndefined();
    }, (error) => {
      expect(error).toBeDefined();
    })
    .finally(done);
}, 10000);

it('should init and send method successfully', (done) => {
  emailjs.init('user_LC2JWGNosRSeMY6HmQFUn');

  emailjs.send('default_service', 'my_test_template', {
    reply_to: 'support@emailjs.com',
    to_name: 'Tester',
    from_name: 'JEST',
    message_html: '<span style="color:#ff5500">Looks like this test is passed</span>'
  }).then((resolve) => {
    expect(resolve).toEqual({'status': 200, 'text': 'OK'});
  }, (error) => {
    expect(error).toBeUndefined();
  }).finally(done);
}, 10000);

it('should send method successfully', (done) => {
  emailjs.send('default_service', 'my_test_template', {
    reply_to: 'support@emailjs.com',
    to_name: 'Tester',
    from_name: 'JEST',
    message_html: '<span style="color:#ff5500">Looks like this test is passed</span>'
  } , 'user_LC2JWGNosRSeMY6HmQFUn')
    .then((resolve) => {
      expect(resolve).toEqual({'status': 200, 'text': 'OK'});
    }, (error) => {
      expect(error).toBeUndefined();
    })
    .finally(done);
}, 10000);

it('should call sendForm method form element validation', () => {
  expect(() => emailjs.sendForm('test', 'test', 'form-not-exist'))
    .toThrow('Expected the HTML form element or the style selector of form');
});

it('should call sendForm with non id selector', (done) => {
  let form: HTMLFormElement = document.createElement('form');
  form.id = 'form-id';
  document.body.appendChild(form);

  emailjs.sendForm('test', 'test', 'form-id')
    .then((resolve) => {
      expect(resolve).toBeUndefined();
    }, (error) => {
      expect(error).toBeDefined();
    })
    .finally(done);
}, 10000);

it('should call sendForm method and fail', (done) => {
  let form: HTMLFormElement = document.createElement('form');

  emailjs.sendForm('test', 'test', form)
    .then((resolve) => {
      expect(resolve).toBeUndefined();
    }, (error) => {
      expect(error).toBeDefined();
    })
    .finally(done);
}, 10000);

it('should call sendForm method successfully', (done) => {
  let form: HTMLFormElement = document.createElement('form');
  let username: HTMLInputElement = document.createElement('input');
  username.name = 'username';
  username.value = 'JEST';
  form.appendChild(username);

  emailjs.sendForm('default_service', 'my_test_template', form, 'user_LC2JWGNosRSeMY6HmQFUn')
    .then((resolve) => {
      expect(resolve).toEqual({'status': 200, 'text': 'OK'});
    }, (error) => {
      expect(error).toBeUndefined();
    })
    .finally(done);
}, 10000);

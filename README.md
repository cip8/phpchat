<a href="https://ciprian.pro"><img src="https://storage.googleapis.com/ciprianpro.appspot.com/images/dominoes/logo-black.png" alt="Ciprian.pro" title="Ciprian.pro" width="110" align="right"></a>

# PHP WebSockets Chat App 

Demo chat app.

<br>

<p align="center">
  <img alt="PHP Chat App Screenshot" src="https://storage.googleapis.com/ciprianpro.appspot.com/images/phpchat/phpchat-preview.gif">
</p>

## Usage

* Clone repository 
* Install [Composer](https://getcomposer.org/) and the project dependencies by running:
  
```sh
$ composer install
```

* Open a new terminal window and run the following command:

```sh
$ ./app start
```

* Access the local URL that points to the "phpchat" folder to preview the app:

```sh
$ http://localhost:[PORT]/phpchat/chat
```

* Alternatively, you can start the Vue development server to preview the front-end. <br>
If you take this approach, make sure to also start the php chat server:

```sh
$ cd [phpchat path]/front
$ yarn serve
```

* The project uses a sqlite database with demo users.

<br>

## FAQ

- **I get a 'permission denied' error - what should I do?**

Try to make the chat app file executable and check if the error persists:
    
```sh
$ chmod +x app
```

- **I have a question...**

Please contact me at hello@ciprian.pro for more details.

## Authors

* **Ciprian Cimpan** - [Ciprian.pro](https://ciprian.pro)

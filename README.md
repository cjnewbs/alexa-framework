# Alexa Skill Framework
**Version: 0.1.1**

I built this to make it simple to create Amazon Alexa skills using PHP. It handles the request verification, intent routing, provides some methods to get intent/slot information from the request and methods for building the response object to AVS.

### To install:
- You can either install just the framework by running `newbury/alexa-framework` or the 
- Run `composer create-project newbury/alexa-app <desired installation directory>`,
- Copy the contents of the contents of the `/example` directory to the application root,
- Set the webserver document root to `/pub`,

### Usage:
Replace `amzn1.ask.skill.xxxxx...` in `config/skills.php` with your alexa skill ID from the developer console. The config file can handle multiple skills by adding an additional array element for each skill.
Under `routes` in the config file add an element under `routes` for each intent you have set in the Alexa developer console. For eample `'example' => \App\Example::class,` the class `\App\Example` will have requests for your `example` intent routed to it.
Create a class for each intent which extends `\Newbury\AlexaFramework\Intent\BaseIntent`. The `execute()` method will be called when an intent is routed to it.

The `Request` and `Response` objects are made available to your intent classes via `$this->request` and `$this->response`.

Methods in `\Newbury\AlexaFramework\Http\Request` are used to get information about the request such as slot data.

Methods in `\Newbury\AlexaFramework\Http\Response` are used to build the response. Most of these methods can be chained together.

You can also call the static method `Newbury\AlexaFramework\Directive::sendDirective($this->request, 'Your message')` to send "directive" responses if you need some time to build the main response.


### Chagelog
##### [0.1.1] - 2018-Apr-13
* Added adility to send "directive" responses,
* Added getters and setters for session attributes,
* Added usage documentation.
##### [0.1.0] - 2018-Apr-12
* Project start,
* Basic intent routing,
* Request getters & response setters,
* Request verification.

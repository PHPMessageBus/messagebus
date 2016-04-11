# Message Bus

Implementation for the CommandBus, the QueryBus and the EventBus in PHP 7.

## What?

The idea of a message bus is that you create message objects that represent what you want your application to do. Then, you toss it into the bus and the bus makes sure that the message object gets to where it needs to go. 

Easy right? Keep reading!

### What is a Message Bus?

A Message Bus is a pipe of Messages. This implementation takes care of 3 types of messages, Commands, Queries and Events. While all look similar at first, their intent is different.

- **Command**: its intent is about expressing an order to the system and modifies its current state. User expects no response. 
- **Event**: its intent is to express something that has already happened in the system, and record it. User expects no response.
- **Query**: its intent is about expressing a question to the system. User expects a response.

From this classification, one can spot that Command and Events can work together very well.

### What are its benefits?

Given the nature of the message, implementing an interface, you may write behaviours that wrap the message to log, process or modify the response using the **Decorator pattern**. These are called **Middleware**.

**For instance:**

- Implementing task-based user-interfaces you can map concepts to Commands, Queries and Events easily.
- It allows you to easily write a logging system to know what's going, whether its a Command, a Query or an Event. It's possible.

**To wrap-up, its benefits are:** 

- Encourages separation of concerns.
- Encourages single responsibility design.

## 1. CommandBus

### 1.1 - Usage

#### 1.1.1 - Create a Command

```php
<?php
use NilPortugues\MessageBus\CommandBus\Contracts\Command;

final class RegisterUser implements Command
{
    private $username;
    private $password;
    private $emailAddress;
    
    public function __construct(string $username, string $password, string $emailAddress)
    {
        $this->username = $username;
        $this->password = $password;
        $this->emailAddress = $emailAddress;
    }
    
    //getters...
}
```

#### 1.1.2 - Create a CommandHandler

The Command Handler must implement the `CommandHandler` interface and implement the `__invoke` method.
 
For instance:

```php
<?php
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandler;
use NilPortugues\MessageBus\CommandBus\Contracts\Command;

final class RegisterUserHandler implements CommandHandler
{
    private $userRepository;
    
    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }
        
    public function __invoke(Command $command)
    {
        $this->guard($command);
        
        $user = new User(
            $command->getUsername(),
            $command->getPassword(),
            $command->getEmail(),
        );
        
        $this->userRepository->save($user);        
    }
    
    private function guard(Command $command)
    {
        if (false === ($command instanceof RegisterUser)) {
            throw new \InvalidArgumentException("Expected command of type: ".RegisterUser::class);
        }
    }
}
```

#### 1.1.3 - Register the CommandHandler

I'm assuming you're using some kind Service Container. Now it's time to register your CommandHandler. 

For instance, in a `Interop\Container` compliant Service Container, we can do this as follows:

```php
<?php
//...your other registered classes

$container['RegisterUserHandler'] = function() use ($container) {
    return new RegisterUserHandler($container['UserRepository']);
};
```

#### 1.1.4 - Setting up the CommandBus


The Command Bus Middleware requires two classes to be injected. First one is the command translator, and second one the handler resolver.

**CommandTranslator**

Classes implementing this interface will provide the FQN for the Handler class given a Command. 

This package provides an implementation, `NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy` which basically appends the word `Handler` to the provided `Command` class.


For custom strategies, you may write your own implementing the `NilPortugues\MessageBus\CommandBus\Contracts\CommandTranslator` interface.

**CommandHandlerResolver**

Classes implementing this interface will be resolving the class for the instance required based on the output of the CommandTranslator used. 

This package provides an implementation, `NilPortugues\MessageBus\CommandBus\Resolver\InteropContainerResolver`, that expects any Service Container implementing the `Interop\Container` interface.

For custom strategies, such as Symfony Container, you may write your own implementing the `NilPortugues\MessageBus\CommandBus\Contracts\CommandHandlerResolver` interface.

#### 1.1.5 - Registering the remaining CommandBus classes

The minimum set up to get the Command Bus working is:

```php
<?php
//...your other registered classes

$container['CommandTranslator'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy('Handler');
};

$container['CommandHandlerResolver'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\Resolver\InteropContainerResolver($container);
};

$container['CommandBusMiddleware'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\CommandBusMiddleware(
        $container['CommandTranslator'],
        $container['CommandHandlerResolver'],
    );
};

$container['CommandBus'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\CommandBus([  
        $container['CommandBusMiddleware'],
    ]);
};
``` 

If for instance, we want to log everything happening in the Command Bus, we'll add to the middleware list the logger middleware. This will wrap the Command Bus, being able to log before and after it ran, and if there was an error.

```php
<?php
//...your other registered classes

$container['LoggerCommandBusMiddleware'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\LoggerCommandBusMiddleware(
        $container['Monolog']
    );
};

//Update the CommandBus with the LoggerCommandBusMiddleware
$container['CommandBus'] = function() use ($container) {
    return new \NilPortugues\MessageBus\CommandBus\CommandBus([
        $container['LoggerCommandBusMiddleware'],
        $container['CommandBusMiddleware'],
    ]);
};
``` 

#### 1.1.6 - Running the CommandBus

Finally, to make use of the CommandBus, all you need to do is run this code: 
```php
<?php
$commandBus = $container->get('CommandBus');
$command = new RegisterUser('MyUsername', 'MySecretPassword', 'hello@example.com');
$commandBus($command);
```


###  1.2 - Predefined Middlewares

**TransactionalCommandBusMiddleware**

- **Class:** `NilPortugues\MessageBus\CommandBus\TransactionalCommandBusMiddleware`
- Class construct method expects a PDO connection. It will wrap all the underlying middleware calls with `beginTransaction-commit` and `rollback` if any kind of exception is thrown.


**LoggerQueryBusMiddleware**

- **Class:** `NilPortugues\MessageBus\CommandBus\LoggerCommandBusMiddleware`
- Class construct method expects a PSR3 Logger implementation.


###  1.3 - Custom Middlewares

----

## 2. QueryBus

### 2.1 - Usage

#### 2.1.1 - Create a Query

```php
<?php
use NilPortugues\MessageBus\QueryBus\Contracts\Query;

final class GetUser implements Query
{
    private $userId;
    
    public function __construct(string $userId) 
    {
        $this->userId = $userId;    
    }
    
    public function getUserId() : string
    {
        return $this->userId;
    }
}
```

#### 2.1.2 - Create a QueryHandler

The Query Handler must implement the `QueryHandler` interface and implement the `__invoke` method.
 
For instance:

```php
<?php
use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandler;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;

final class GetUserHandler implements QueryHandler
{
    private $userRepository;
    
    public function __construct($userRepository)
    {
        $this->userRepository = $userRepository;
    }
        
    public function __invoke(Query $query) : QueryResponse
    {
        $this->guard($query);        
        $userId = $query->getUserId();        
        $user = $this->userRepository->find($userId);       
         
        return new UserQueryResponse($user);
    }
    
    private function guard(Query $query)
    {
        if (false === ($query instanceof GetUser)) {
            throw new \InvalidArgumentException("Expected query of type: ".GetUser::class);
        }
    }
}
```

#### 2.1.3 - Create a QueryResponse

Response queries are generic responses.

If you take into account section 2.1.2, you'll see the UserQueryResponse has a $user injected into the constructor. 
This has been done in order to reuse the QueryResponse in other scenarios such as fetching an updated user.

```php
<?php

class UserQueryResponse implements QueryResponse
{
   public function __construct(User $user)
   {
        //fetch the relevant properties from the User object
   }
   
   //..getters. No setters required!
}
```

#### 2.1.4 - Register the QueryHandler

I'm assuming you're using some kind Service Container. Now it's time to register your QueryHandler. 

For instance, in a `Interop\Container` compliant Service Container, we can do this as follows:

```php
<?php
//...your other registered classes

$container['GetUserHandler'] = function() use ($container) {
    return new GetUserHandler($container['UserRepository']);
};
```

#### 2.1.5 - Setting up the QueryBusMiddleware

The Query Bus Middleware requires two classes to be injected. First one is the query translator, and second one the handler resolver.

**QueryTranslator**

Classes implementing this interface will provide the FQN for the Handler class given a Query. 

This package provides an implementation, `NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy` which basically appends the word `Handler` to the provided `Query` class.


For custom strategies, you may write your own implementing the `NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator` interface.

**QueryHandlerResolver**

Classes implementing this interface will be resolving the class for the instance required based on the output of the QueryTranslator used. 

This package provides an implementation, `NilPortugues\MessageBus\QueryBus\Resolver\InteropContainerResolver`, that expects any Service Container implementing the `Interop\Container` interface.

For custom strategies, such as Symfony Container, you may write your own implementing the `NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver` interface.


#### 2.1.5 - Registering the remaining QueryBus classes

The minimum set up to get the Query Bus working is:

```php
<?php
//...your other registered classes

$container['QueryTranslator'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy('Handler');
};

$container['QueryHandlerResolver'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\Resolver\InteropContainerResolver($container);
};

$container['QueryBusMiddleware'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\QueryBusMiddleware(
        $container['QueryTranslator'],
        $container['QueryHandlerResolver'],
    );
};

$container['QueryBus'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\QueryBus([  
        $container['QueryBusMiddleware'],
    ]);
};
``` 

If for instance, we want to log everything happening in the Query Bus, we'll add to the middleware list the logger middleware. This will wrap the Query Bus, being able to log before and after it ran, and if there was an error.

```php
<?php
//...your other registered classes

$container['LoggerQueryBusMiddleware'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\LoggerQueryBusMiddleware(
        $container['Monolog']
    );
};

//Update the QueryBus with the LoggerQueryBusMiddleware
$container['QueryBus'] = function() use ($container) {
    return new \NilPortugues\MessageBus\QueryBus\QueryBus([
        $container['LoggerQueryBusMiddleware'],
        $container['QueryBusMiddleware'],
    ]);
};
``` 

#### 2.1.6 - Running the QueryBus

Finally, to make use of the QueryBus, all you need to do is run this code: 
```php
<?php
$queryBus = $container->get('QueryBus');
$query = new GetUser(1):
$userQueryResponse = $queryBus($query);
```


###  2.2 - Predefined Middlewares

**CacheQueryBusMiddleware**

- Class: `NilPortugues\MessageBus\QueryBus\CacheQueryBusMiddleware`
- Class construct method expects a Serializer (see below), a PSR6 Caching implementation and queue name.

**LoggerQueryBusMiddleware**

- Class: `NilPortugues\MessageBus\QueryBus\LoggerQueryBusMiddleware`
- Class construct method expects a PSR3 Logger implementation.


###  2.3 - Custom Middlewares

----

## 3. EventBus

### 3.1 - Usage


#### 3.1.1 - Create an Event

We'll be creating an Event. Due to the nature of events, an event may be mapped to one or more Event Handlers.

```php
<?php
use NilPortugues\MessageBus\EventBus\Contracts\Event;

final class UserRegistered implements Event
{
    private $userId;
    private $email;
    
    public function __construct(string $userId, string $email) 
    {
        $this->userId = $userId;    
        $this->email = $email;    
    }
    
    public function getUserId() : string
    {
        return $this->userId;
    }
    
    public function getEmail() : string
    {
        return $this->email;
    }
}
```

#### 3.1.1 - Create an EventHandler

To illustrate the power of eventing, we'll map the previous event `UserRegistered` to two EventHandlers.

**First Event Handler**

First event handler we'll create assumes we've got an email service to send a welcome email.

```php
<?php
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;

final class SendWelcomeEmailHandler implements EventHandler
{
    private $emailProvider;
    
    public function __construct($emailProvider)
    {
        $this->emailProvider = $emailProvider;
    }
        
    public function __invoke(Event $event)
    {
        $this->guard($event);        
        $this->emailProvider->send('welcome_email', $event->getEmail());        
    }
    
    private function guard(Event $event)
    {
        if (false === ($event instanceof UserRegistered)) {
            throw new \InvalidArgumentException("Expected event of type: ".UserRegistered::class);
        }
    }
        
    public static function subscribedTo() : string
    {
        return UserRegistered::class;
    }
}
```

**Second Event Handler**

Second event handler we'll create the relationships in our database for user friends and user credits.

```php
<?php
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;

final class SetupUserAccountHandler implements EventHandler
{
    private $userFriendsRepository;
    private $userCreditsRepository;
    
    public function __construct($userFriendsRepository, $userCreditsRepository)
    {
        $this->userFriendsRepository = $userFriendsRepository;
        $this->userCreditsRepository = $userCreditsRepository;
    }
        
    public function __invoke(Event $event)
    {
        $this->guard($event);        
        $this->userFriendsRepository->add(new UserFriendsCollection($event->getUserId(), []));        
        $this->userCreditsRepository->add(new UserCredits($event->getUserId(), new Credits(0));        
    }
    
    private function guard(Event $event)
    {
        if (false === ($event instanceof UserRegistered)) {
            throw new \InvalidArgumentException("Expected event of type: ".UserRegistered::class);
        }
    }
        
    public static function subscribedTo() : string
    {
        return UserRegistered::class;
    }
}
```


#### 3.1.2 - (Optional) Set the EventHandler's Priority

Sometimes you want or must make sure an action precedes another one.
 
By default, all events have a priority, this being set by the `EventHandlerPriority::LOW_PRIORITY` constant value.

To implement your priority order in your classes implement the `EventHandler` interface, must implement another interface, the `EventHandlerPriority`. 

For instance, if we would like `SendWelcomeEmailHandler` to happen after `SetupUserAccountHandler`, we should give the first less priority, or the latter more.

**SetupUserAccountHandler should go first when dispatching event UserRegistered**

```php
<?php
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerPriority;

final class SetupUserAccountHandler implements EventHandler, EventHandlerPriority
{
   //same as before...
   
   public static function priority() : int
   {
        return EventHandlerPriority::MAX_PRIORITY;
   }
}
```

**SendWelcomeEmailHandler should go second when dispatching event UserRegistered**

Notice how a good idea is to set up relative ordering by subtracting to the MAX_PRIORITY order.

```php
<?php
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerPriority;

final class SendWelcomeEmailHandler implements EventHandler, EventHandlerPriority
{
   //same as before...
   
   public static function priority() : int
   {
        return EventHandlerPriority::MAX_PRIORITY - 1;
   }
}
```

#### 3.1.4 - Register the EventHandler
#### 3.1.5 - Setting up the EventBusMiddleware
#### 3.1.5 - Registering the remaining EventBus classes
#### 3.1.6 - Running the EventBus


#### 3.1.7 - (Optional) Running the EventBus as a Queue

To do so, you'll have to require an additional package: **EventBus Queue**. This extension can be downloaded using composer:

```
composer require nilportugues/eventbus-queue
```

[Documentation can be found in its repository](https://github.com/PHPMessageBus/event-bus-queue).



### 3.2 - Predefined Middlewares

### 3.3 - Custom Middlewares

---

## 4 - Serializers

Serializers are to be used mainly all the `<Name>ProducerEventBusMiddleware` classes. You may also find this in Cache classes.

Choose one or another depending on your needs.

### 4.1 - NilPortugues\MessageBus\Serializer\NativeSerializer

For caching, this is the best option.

In the EventBus use this if your Consumer is written in PHP and will share the same code base as the object serialized.

- **Pros:** Fastest serialization possible.
- **Cons:** Consumer must be written in PHP and classes must be available or unserialize will fail.

### 4.2 - NilPortugues\MessageBus\Serializer\JsonSerializer

Not recommended for caching.

In the EventBus  use this if your Consumer is written in PHP but your consumers may be written in many languages. 

- **Pros:** If consumer is PHP, the data will be restored and if sharing the same code base as the object serialized, an object can be obtained on unserialize. If not, you may be able to fetch data manually as regular JSON.
- **Cons** : If fetching data from the data-store as JSON it will hold references to the PHP data-structure, but does not interfere consuming data.
 
### 4.3 - NilPortugues\MessageBus\Serializer\JsonObjectSerializer

Doesn't work for caching.

In the EventBus use this if your Consumer is written in PHP but you're not consuming data currently.  

- **Pros:** JSON can be reused to consume this data in the future as its supported everywhere.
- **Cons:** You'll have to write your consumer to read the JSON structure.


## Contribute

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker](https://github.com/PHPMessageBus/message-bus/issues/new).
* You can grab the source code at the package's [Git repository](https://github.com/PHPMessageBus/message-bus).


## Support

Get in touch with me using one of the following means:

 - Emailing me at <contact@nilportugues.com>
 - Opening an [Issue](https://github.com/PHPMessageBus/message-bus/issues/new)


## Authors

* [Nil Portugués Calderó](http://nilportugues.com)
* [The Community Contributors](https://github.com/PHPMessageBus/message-bus/graphs/contributors)


## License
The code base is licensed under the [MIT license](LICENSE).

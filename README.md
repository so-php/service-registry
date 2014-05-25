# service-registry

Service Registry Library built on top of php-amqplib and Zookeeper php extension.


# Service Registry Pattern
To explain the service registry pattern lets take a look at how a programmer might utilize a service in some typical scenarios.

## Without Service Registry Pattern
Using a service that is available locally

    $service = new Service();
    $service->method();

Using a service that is available remotely

    $rpc = new XmlRpc('path/to/service/descriptor.wsdl');
    $rpc->call('method');

Using a service that is available remotely and _may_ be available locally

    if(class_exists('Service')){
        $service = new Service();
        $service->method();
    } else {
        $rpc = new XmlRpc('path/to/service/descriptor.wsdl');
        $rpc->call('method');
    }

Using a service that is available from multiple remote services using a decision strategy like load-balancing, or A\B testing to pick a remote provider

    ??? what?

Unfortunately that kind of behaviour can't even be pseudo-coded in a simple enough manner to provide a sample. And there's not really a common methodology to implementing such features--you'd be on your own if you wanted to add such behavior to your app.

## With Service Registry Pattern
Lets see how a service registry pattern simplifies these scenarios.

Using a service that is available locally

    $service = ServiceRegistry::getInstance('Service');
    $service->method();

Using a service that is available remotely

    $service = ServiceRegistry::getInstance('Service');
    $service->method();

Using a service that is available remotely and _may_ be available locally

    $service = ServiceRegistry::getInstance('Service');
    $service->method();

Using a service that is available from multiple remote services using a decision strategy like load-balancing, or A\B testing to pick a remote provider

    ServiceRegistry::setStrategy(new RoundRobin());
    // or:  ServiceRegistry::setStrategy(new FirstAvailable());
    // or:  ServiceRegistry::setStrategy(new ABTesting());

    $service = ServiceRegistry::getInstance('Service');
    $service->method();

Note how that in all of the scenarios the programmer is faced with exactly the same usage of `Service`.

So, simply put, the service registry pattern is a way to register instances of a service (be it locally, or remotely). Then, you can ask the registry for a reference to one of those instances.

Now, the service registry pattern solves many more problems that haven't been touched on. Things like being able to provide multiple (different) implementations for a service, or providing multiple versions of any given implementation of a service. Lots of cool things that we're not going to go into detail here.

The `Service Registry` library itself provides an implementation of this pattern that orchestrates the mechanics of registering, un-registering and providing service instances whether they be available locally or remotely. If the service instance chosen happens to be remotely available, it hides that complexity away from you.
Invisibly it provides you with a proxy that perfectly mirrors the service interface so that you can treat it like a regular Service instance.

# Theory of Operation
As it's name suggests, one of the necessities of the pattern is keeping track of services (that have been potentially instantiated in numerable different processes and even servers. On top of maintaining a list of those services, potentially an Remote Procedure Calls (RPC) may need to be orchestrated between an instance reference proxy and the actual instance on a remote server.

Service registration is implemented with Zookeeper, registrations for each instance are stored under ephemeral nodes.

RPC is provided on top of Active MQ Protocol (AMQP) via [php-amqplib](https://github.com/videlalvaro/php-amqplib) (specifically flavored for [RabbitMQ](http://www.rabbitmq.com/)).


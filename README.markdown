Oryx Bundle
=======

The Oryx open source project provides simple, real-time large-scale machine learning / predictive analytics infrastructure. It is built on the [Apache Mahout libraries](http://mahout.apache.org/) and represents a unified continuation of the [Myrrix](http://myrrix.com) and
[cloudera/ml](https://github.com/cloudera/ml) projects. You can check it out [here](https://github.com/cloudera/oryx).

This bundle helps to interface with the Oryx Collaborative filtering and Recommendation services. 

Installation & configuration
----------------------

### Install Oryx

See the [Oryx doc](https://github.com/cloudera/oryx/wiki/Installation) for installation, you can get releases [here](https://github.com/cloudera/oryx/releases).

### Install the bundle

#### Get the Bundle via Composer

In the command line : 
```
composer require fmm/oryx-recommend
```

Manually : add guzzle and fmm/oryx-recommend to your composer.json : 
```
{
  "require": 
  {
    "guzzle/guzzle": "dev-master",
    "fmm/oryx-recommend": "dev-master"
  }
}
```
and then install the dependencies with `composer install`.

#### Add the bundle to your kernel

```
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new FMM\OryxBundle\FMMOryxBundle(),
        // ...
    );
}
```

#### Set the configuration

You have to configure the Oryx endpoint configuration :
```
// app/config/config.yml
fmm_oryx:
    host: localhost # The Oryx host
    port: 8080      # The Oryx port
    username: test  # The Oryx username
    password: 1234  # The Oryx password

```

Usage
-----


TODO
-----

### Info
The code was developped and tested with *Oryx 0.3.0*

### Data format
For now, data is received only in `.csv` format, `json` should be included in Oryx' next version and support added to the bundle


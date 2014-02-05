Oryx Bundle
=======

The Oryx open source project provides simple, real-time large-scale machine learning / predictive analytics infrastructure. It is built on the [Apache Mahout libraries](http://mahout.apache.org/) and represents a unified continuation of the [Myrrix](http://myrrix.com) and
[cloudera/ml](https://github.com/cloudera/ml) projects. You can check it out [here](https://github.com/cloudera/oryx).

This bundle helps to interface with the Oryx Collaborative filtering and Recommendation services. It is built on top of [Guzzle](https://github.com/guzzle/guzzle) and inspired by [michelsalib's](https://github.com/michelsalib) [BCCMyrrixBundle](https://github.com/michelsalib/BCCMyrrixBundle) and [bcc-myrrix](https://github.com/michelsalib/bcc-myrrix) projects.

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
```json
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

```php
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
```yml
// app/config/config.yml
fmm_oryx:
    host: localhost # The Oryx host
    port: 8080      # The Oryx port
    username: test  # The Oryx username
    password: 1234  # The Oryx password

```

#### Run your Oryx server and computation instances

Create a file called `oryx.conf` with the following informations : 

```
model=${als-model}
model.local-computation=false
model.local-data=false
model.instance-dir=/user/name/repo
model.features=25
model.lambda=0.065
serving-layer.api.port=8091
computation-layer.api.port=8092
```

And run the jars with the following lines : 

```
java -Dconfig.file=oryx.conf -jar oryx-computation-x.y.z.jar
java -Dconfig.file=oryx.conf -jar oryx-serving-x.y.z.jar
```

Usage
-----

Get an instance of `OryxService` :

```php
// Get an instance
$oryx = $this->get('fmm_oryx.service');

// Set a user/item association between user #22, item #888 of strength 0.8
$oryx->setPreference(22, 888, 0.8);

// Refresh the index
$oryx->refresh();

// Get recommendations as csv
$csv = $oryx->getRecommendation(22); // example : 325,0.5\n98,0.44

// Parse it and get an array of strings "id,strength"
$recommendations = str_getcsv($csv, "\n");
```


DEVELOPMENT
------------

### Info
The code was developped and tested with *Oryx 0.3.0*

### TODO

#### Data format

For now, data is received only as a `csv` string, `json` should be included in Oryx' next version and support added to the bundle.
Also make it so that we don't have to parse the received `csv` data.

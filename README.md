Grommet ApiLog
==============

Magento module for logging SOAP and XMLRPC requests made to Magento's API. An admin interface is provided to view logged requests.

Supported API types:

* SOAP
* SOAP V2
* XMLRPC

TODO

* Support SOAP WSI
* Support API2
* Request timing & profiling

Features
--------

* Enable/disable
* Log view ACL and Enterprise admin action logging support
* Log responses by API path
* Cron cleanup of old logs

Installation
------------
Copy repository contents in a Magento root directory. Clear cache and navigate to _System -> Web Services -> SOAP/XML-RPC - Request Log_

Configuration
-------------
In the Magento admin, see _System -> Configuration -> System / API Logging_

Requirements
------------

* Magento 1.6+
* PHP 5.4+ (due to use of Traits)

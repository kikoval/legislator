Legislator
========================

Legislator is a tool to help create law documents.

After adding a document to the system it can under o several step process that can be repeaded.
  1. Commenting phase
  2. Comments review phase (accepting/rejecting comments)
  3. Uploading a new version of the document

The document from the last iteration can be marked as final.

## Installation ##

1. clone (`git closne <repo_url>`) the repository into a directory and `cd` to it
2. get the vendor dependencies using [Composer](http://getcomposer.org/) `php composer.phar install`
3. copy `app/config/parameters.yml.dist` to `app/config/parameters.yml` and edit the parameters
3. initialize the database `php app/console doctrine:schema:create`
4. install assets `php app/console assetic:dump --env=prod --no-debug`
4. run the application in a dev webserver `app/console server:run`

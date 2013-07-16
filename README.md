Legislator
========================

Legislator is a tool to help create law documents.

After adding a document to the system it can undergo several process phases:
  1. Commenting phase
    * users (that have access to the document) post their comments
  2. Comments review phase
    * commenting is disabled and the the document creator evaluates the comments
  3. Uploading a new version of the document
    * the new version can be marked as final when the process stops othervise it continues with phase 1

Users can be created using registration or are added automatically when using CoSign. Users can be added to groups. Groups can be associated to documents to work as whitelist (documents without a group is open to everybody).

The application is by default accessible only to authorized users.

## Installation ##

### Requirements ###

* [Symfony 2.3 requirements](http://symfony.com/doc/current/reference/requirements.html)
* [Composer](http://getcomposer.org/) for package management
* [Git](http://git-scm.com/) for cloning the repository


### Installation steps ###

1. get the `.zip` or `.tgz` archive and extract it or clone (`git clone <repo_url>`) the repository into a directory and `cd` to it
2. get the vendor dependencies using [Composer](http://getcomposer.org/) `php composer.phar install`
  * during the process, you'll be prompted to edit the parameters (database, etc.)
3. initialize the database `php app/console doctrine:schema:create`
4. install assets `php app/console assetic:dump --env=prod --no-debug`
4. run the application in a dev webserver `php app/console server:run`

## Licence ##

See the `LICENCE` file.

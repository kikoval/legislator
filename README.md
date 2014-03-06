Legislator
========================

Legislator is a web application that aims to help to create documents that needs to be review by multiple people, such as legal documents.

After adding a document to the application it can undergo several process phases:
  1. Commenting phase
    * users (that have access to the document) post their comments
    * can have a deadline
  2. Comments review phase
    * commenting is disabled and the the document creator evaluates the comments
  3. Uploading a new version of the document
    * the new version can be marked as final when the process stops otherwise it continues with phase 1

The document creator can change the order of the comments, so it's easier for him process them during the review phase.

By default, the application is accessible only to authorized users (except the login page).

Users can be added using registration or they can be added automatically, when using CoSign. Users can be added to groups. Documents can have access restricted only to certain groups. Documents without any assigned group are open to every user.


## Installation ##

[![Build Status](https://travis-ci.org/kikoval/legislator.png)](https://travis-ci.org/kikoval/legislator)

### Requirements ###

* [Symfony 2.3 requirements](http://symfony.com/doc/2.3/reference/requirements.html)
* [Composer](http://getcomposer.org/) for package management
* [Git](http://git-scm.com/) for cloning the repository (optional)

### Installation steps ###

1. Grab the code, there are essentially two options for this:
  * get the `.zip` or `.tgz` archive and extract it or
  * clone (`git clone <repo_url>`) the repository into a directory and `cd` to it
2. Get the vendor dependencies using [Composer](http://getcomposer.org/) `php composer.phar install`
  * during the process, you'll be prompted to edit the parameters (database, etc.), you can edit them afterwards in `app/config/parameters.yml`
3. Initialize the database `php app/console doctrine:schema:create`
4. Install assets `php app/console assetic:dump`. For production add `--env=prod --no-debug` parameters.
5. Import default admin user `php app/console doctrine:fixtures:load`
  * login credentials: `admin:test` (don't forget to **change the password**)
  * the admin user can assign admin role to other users

To run the application in a dev web server execute `php app/console server:run`.

### CoSign ###

To enable [CoSign](http://www.weblogin.org/) authentication:

1. In `app/config/paramters.yml`, set `cosign_login_enabled` to `TRUE`. Make sure the `cosign_logout_prefix` parameters has a correct value.
2. Follow instructions in `app/config/security.cosign.yml.dist`.


## License ##

See the `LICENSE` file.

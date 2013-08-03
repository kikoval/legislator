<?php

namespace Legislator\LegislatorBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\DBAL\Driver\PDOSqlite\Driver as SqliteDriver;

class MyWebTestCase extends WebTestCase
{
	protected $client = null;
	protected static $application = null;
	protected static $isFirstTest = TRUE;

	public function setUp()
	{
		$this->client = static::createClient();

		self::$application = new Application($this->getKernel());
		self::$application->setAutoExit(false);

		if (!$this->useCachedDatabase()) {
			$this->databaseInit();
		}
	}

	/**
	 * Get an instance of the dependency injection container.
	 * (this creates a kernel *without* parameters).
	 *
	 * @return object
	 */
	protected function getContainer()
	{
		return $this->client->getContainer();
	}

	protected function getKernel()
	{
		return $this->client->getKernel();
	}


	/**
	 * Extracts the location from the given route.
	 *
	 * @param string $route  The name of the route
	 * @param array $params  Set of parameters
	 * @param boolean $absolute
	 *
	 * @return string
	 */
	protected function getUrl($route, $params = array(), $absolute = false)
	{
		return $this->getContainer()->get('router')->generate($route, $params, $absolute);
	}

	protected function logIn()
	{
		$crawler = $this->client->request('GET', $this->getUrl('fos_user_security_login'));

		// make sure we are on login page
		$this->assertCount(1, $crawler->filter('form'));
		$this->assertCount(1, $crawler->filter('#_submit'));

		// log in using form
		$form = $crawler->selectButton('_submit')->form();
		$this->client->submit($form, array('_username' => 'tester', '_password' => 'test'));

		// redirecting to main page
		$this->assertTrue($this->client->getResponse()->isRedirect());
		$crawler = $this->client->followRedirect();
		$this->assertTrue($this->client->getResponse()->isSuccessful());
	}


	/**
	 * Initialize database
	 */
	protected function databaseInit()
	{
		$this->runConsole("doctrine:schema:drop", array("--force" => true));
		$this->runConsole("doctrine:schema:create");
		$this->runConsole("cache:warmup");
	}

	/**
	 * Use cached database for testing or return false if not
	 */
	protected function useCachedDatabase()
	{
		$container = $this->getContainer();
		$om = $container->get('doctrine')->getManager();
		$connection = $om->getConnection();

		if ($connection->getDriver() instanceOf SqliteDriver) {
			$params = $connection->getParams();
			$name = isset($params['path']) ? $params['path'] : $params['dbname'];
			$filename = pathinfo($name, PATHINFO_BASENAME);
			$backup = $container->getParameter('kernel.cache_dir') . '/'.$filename;

			// The first time we won't use the cached version
			if (self::$isFirstTest) {
				self::$isFirstTest = false;
				return false;
			}

			self::$isFirstTest = false;

			// Regenerate not-existing database
			if (!file_exists($name)) {
				@unlink($backup);
				return false;
			}

			$om->flush();
			$om->clear();

			// Copy backup to database
			if (!file_exists($backup)) {
				copy($name, $backup);
			}

			copy($backup, $name);
			return true;
		}

		return false;
	}

	/**
	 * Load tests fixtures
	 */
	protected function loadFixtures()
	{
		$this->runConsole("doctrine:fixtures:load");
	}

	/**
	 * Executes a console command
	 *
	 * @param type $command
	 * @param array $options
	 * @return type integer
	 */
	protected function runConsole($command, Array $options = array())
	{
		$options["--env"] = "test";
		$options["--quiet"] = null;
		$options["--no-interaction"] = null;
		$options = array_merge($options, array('command' => $command));
		return self::$application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
	}

}

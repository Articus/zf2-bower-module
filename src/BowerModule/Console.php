<?php

namespace BowerModule;


use BowerModule\Bower;
use BowerModule\Config;
use BowerModule\PathBuilder;
use BowerModule\ServiceRetriever;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;
use Zend\Console\ColorInterface as C;

class Console extends AbstractConsoleController
{
	use ServiceRetriever\ServiceLocatorTrait;
	use Bower\ServiceAwareTrait;
	use Config\ServiceAwareTrait;
	use PathBuilder\ServiceAwareTrait;

	public function preparePacksAction()
	{
		$config = $this->getConfig();
		$bower = $this->getBower();
		$pathBuilder = $this->getPathBuilder();
		$console = $this->getConsole();

		$append = function($pathFrom, $pathTo) use ($console)
		{
			$result = false;

			$fileFrom = fopen($pathFrom, 'r');
			$fileTo = fopen($pathTo, 'a');
			if ($fileFrom === false)
			{
				$console->writeLine(sprintf('Failed to open "%s" for reading.', $pathFrom), C::RED);
			}
			elseif ($fileTo === false)
			{
				$console->writeLine(sprintf('Failed to open "%s" for appending.', $pathTo), C::RED);
			}
			else
			{
				$readLimit = 1000;
				while (!feof($fileFrom))
				{
					$readResult = fread($fileFrom, $readLimit);
					if ($readResult === false)
					{
						throw new \RuntimeException(sprintf('Failed to read data from "%s".', $pathFrom));
					}
					$writeResult = fwrite($fileTo, $readResult);
					if ($writeResult === false)
					{
						throw new \RuntimeException(sprintf('Failed to write data to "%s".', $pathTo));
					}
				}
				//
				if (fwrite($fileTo, "\n\n") === false)
				{
					throw new \RuntimeException(sprintf('Failed to write line end to "%s".', $pathTo));
				}
				fclose($fileFrom);
				fclose($fileTo);
				$result = true;
			}
			return $result;
		};

		$reset = function($path) use ($console)
		{
			if (is_writable($path))
			{
				$console->write(sprintf('Deleting exiting "%s". ', $path), C::YELLOW);
				$removeResult = unlink($path);
				if ($removeResult === false)
				{
					throw new \RuntimeException(sprintf('Failed to delete existing "%s".', $path), C::RED);
				}
			}
		};
		foreach ($config->getPacks() as $packName => $pack)
		{
			try
			{
				$console->write(sprintf('Processing pack "%s". ', $packName), C::GREEN);
				$packPath = $pathBuilder->getPackOsPath($packName);
				$reset($packPath);
				$modules = $bower->findDependencies($pack->getModules());
				$console->writeLine(sprintf('Consists of %d modules.', count($modules)));
				foreach ($modules as $moduleName)
				{
					$console->write(sprintf('Processing module "%s". ', $moduleName));
					$modulePaths = [];
					try
					{
						$modulePaths = $bower->getModulePaths($moduleName, true);
					}
					catch (\RuntimeException $e)
					{
						$console->write(sprintf('Failed to retrieve minified code for module "%s". ', $moduleName), C::RED);
						$modulePaths = $bower->getModulePaths($moduleName);
					}
					$console->writeLine(sprintf('Consists of %d files.', count($modulePaths)));
					foreach ($modulePaths as $modulePath)
					{
						$console->write(sprintf('Processing file "%s"... ', $modulePath));
						$append($modulePath, $packPath);
					}
//					if ($config->getDebugMode())
//					{
						$console->writeLine();
						$console->write(sprintf('Preparing debug sources for module "%s". ', $moduleName), C::GREEN);
						$debugPath = $pathBuilder->getDebugOsPath($moduleName);
						$reset($debugPath);
						$modulePaths = $bower->getModulePaths($moduleName);
						foreach ($modulePaths as $modulePath)
						{
							$console->write(sprintf('Processing file "%s"... ', $modulePath));
							$append($modulePath, $debugPath);
						}

//					}
					$console->writeLine();
				}
				$console->writeLine(sprintf('Pack hash "%s".', md5_file($packPath)), C::YELLOW);
				$console->writeLine();
			}
			catch (\Exception $e)
			{
				$console->writeLine(sprintf('Failed to process pack "%s"', $packName), C::RED);
				$console->writeLine($e);
			}
		}

		return new ConsoleModel();
	}
}
<?php

namespace BowerModule\Config;


use Zend\Stdlib\AbstractOptions;

/**
 * Configuration options for whole module
 */
class Module extends AbstractOptions
{
	/**
	 * The "bower_components" folder where all bower modules are located
	 * Only OS path is used.
	 * @var Folder
	 */
	protected $bowerFolder;
	/**
	 * The folder where all pack JS files will be placed.
	 * @var Folder
	 */
	protected $packFolder;
	/**
	 * Pack configurations.
	 * @var Pack[]
	 */
	protected $packs = [];
	/**
	 * The folder where source code of each bower module will be placed for debug mode.
	 * @var Folder
	 */
	protected $debugFolder;
	/**
	 * Flag if debug mode is enabled.
	 * In normal mode each pack is represented with single JS file made from combined minified bower modules' JS files.
	 * In debug mode each pack is represented with a list of all non-minified bower modules JS files.
	 * @var bool
	 */
	protected $debugMode;
	/**
	 * Set to true if you want to scan for package.json file in addition to bower.json
	 * @var bool
	 */
	protected $usePackageJson = false;
	
	/**
	 * @param array|Folder $bowerFolder
	 */
	public function setBowerFolder($bowerFolder)
	{
		if (is_array($bowerFolder))
		{
			$bowerFolder = new Folder($bowerFolder);
		}
		elseif (!($bowerFolder instanceof Folder))
		{
			throw new \RuntimeException('Invalid bower folder configuration');
		}
		$this->bowerFolder = $bowerFolder;
	}

	/**
	 * @return Folder
	 */
	public function getBowerFolder()
	{
		return $this->bowerFolder;
	}

	/**
	 * @param array|Folder $packFolder
	 */
	public function setPackFolder($packFolder)
	{
		if (is_array($packFolder))
		{
			$packFolder = new Folder($packFolder);
		}
		elseif (!($packFolder instanceof Folder))
		{
			throw new \RuntimeException('Invalid pack folder configuration');
		}
		$this->packFolder = $packFolder;
	}

	/**
	 * @return Folder
	 */
	public function getPackFolder()
	{
		return $this->packFolder;
	}

	/**
	 * @param Pack[]|array $packs
	 */
	public function setPacks(array $packs)
	{
		foreach ($packs as $key => $pack)
		{
			if (is_array($pack))
			{
				$pack = new Pack($pack);
				$packs[$key] = $pack;
			}
			if (!($pack instanceof Pack))
			{
				throw new \RuntimeException('Invalid pack options.');
			}
		}
		$this->packs = $packs;
	}

	/**
	 * @return Pack[]
	 */
	public function getPacks()
	{
		return $this->packs;
	}

	/**
	 * @param array|Folder $debugFolder
	 */
	public function setDebugFolder($debugFolder)
	{
		if (is_array($debugFolder))
		{
			$debugFolder = new Folder($debugFolder);
		}
		elseif (!($debugFolder instanceof Folder))
		{
			throw new \RuntimeException('Invalid debug folder configuration');
		}
		$this->debugFolder = $debugFolder;
	}

	/**
	 * @return Folder
	 */
	public function getDebugFolder()
	{
		return $this->debugFolder;
	}

	/**
	 * @param boolean $debugMode
	 */
	public function setDebugMode($debugMode)
	{
		$this->debugMode = $debugMode;
	}

	/**
	 * @return boolean
	 */
	public function getDebugMode()
	{
		return (bool) $this->debugMode;
	}
	
	public function getUsePackageJson() {
		return (bool) $this->usePackageJson;
	}

	public function setUsePackageJson($usePackageJson) {
		$this->usePackageJson = $usePackageJson;
	}
}
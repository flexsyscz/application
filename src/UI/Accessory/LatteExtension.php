<?php

declare(strict_types=1);

namespace Flexsyscz\Application\UI\Accessory;

use Flexsyscz\Application\Exceptions\AssetNotFoundException;
use Flexsyscz\FileSystem\Directories\AssetsDirectory;
use Latte\Extension;
use Nette\Http\Request;
use Nette\Utils\FileSystem;


class LatteExtension extends Extension
{
	public function __construct(
		private readonly Request $httpRequest,
		private readonly AssetsDirectory $assetsDirectory,
	)
	{
	}


	public function getFilters(): array
	{
		return [
			'humanFileSize' => $this->filterHumanFileSize(...),
		];
	}


	public function getFunctions(): array
	{
		return [
			'asset' => $this->asset(...),
		];
	}


	public function filterHumanFileSize(int $size, int $precision = 2): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];

		$bytes = max($size, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);
		return round($bytes, $precision) . $units[$pow];
	}


	public function asset(string $path): string
	{
		if ($this->assetsDirectory->exists($path)) {
			$absolutePath = FileSystem::normalizePath($this->assetsDirectory->getAbsolutePath($path));
			$hash = hash_file('sha256', $absolutePath);

			$path = FileSystem::normalizePath($this->assetsDirectory->getRelativePath($path));
			$basePath = $this->httpRequest->getUrl()->getBasePath();
			return sprintf('%s%s/%s?hash=%s', $this->httpRequest->getUrl()->getHostUrl(), $basePath === '/' ? '' : $basePath, $path, $hash);
		}

		throw new AssetNotFoundException(sprintf("Asset '%s' not found.", $path));
	}
}
